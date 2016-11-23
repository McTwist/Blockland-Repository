<?php
namespace App\Repository\Archive;

/*
|--------------------------------------------------------------------------
| Archive
|--------------------------------------------------------------------------
|
| Handles an archive and all its content. Applies easy functionality to
| access each file. Parsing and modification is done through ArchiveFile
|
*/
class Archive
{
	protected $archive_name = '';
	private $archive = null;

	private $filetype_count = [];

	private $filetype_readers = [];
	private $file_readers = [];

	// Convenient common newline
	const NL = "\r\n";

	public function __construct($file)
	{
		$this->archive_name = $file;
	}

	public function __destruct()
	{
		// It is a rule to not save anything unless explicitly said so
		if ($this->IsOpen())
			$this->Abort();
	}

	// Open a file to be read
	public function Open($file)
	{
		$archive = new \ZipArchive;
		if ($archive->open($file) !== true)
			return false;

		$this->archive = $archive;

		$this->CountFileTypes();

		return true;
	}

	// Check if archive is open
	public function IsOpen()
	{
		return $this->archive !== null;
	}

	// Save and close the archive
	public function Close()
	{
		$this->archive->close();
		$this->archive = null;
	}

	// Undo all changes and close the archive
	public function Abort()
	{
		$this->archive->unchangeAll();
		$this->Close();
	}

	// Add file reader
	// file - File to find
	// reader - Class name for reader
	public function AddFileReader($file, $reader)
	{
		if (!empty($file) && class_exists($reader) && is_subclass_of($reader, ArchiveFile::class))
		{
			if (is_array($file))
			{
				$files = array_map('strtolower', $file);
				foreach($files as $file)
				{
					$this->file_readers[$file] = $reader;
				}
			}
			else
			{
				$file = strtolower($file);
				$this->file_readers[$file] = $reader;
			}
		}
	}

	// Add file type reader
	// type - Extension of the file type
	// reader - Class name for reader
	public function AddFileTypeReader($type, $reader)
	{
		if (!empty($type) && class_exists($reader) && is_subclass_of($reader, ArchiveFile::class))
		{
			if (is_array($type))
			{
				$types = array_map('strtolower', $type);
				foreach($types as $type)
				{
					$this->filetype_readers[$type] = $reader;
				}
			}
			else
			{
				$type = strtolower($type);
				$this->filetype_readers[$type] = $reader;
			}
		}
	}

	// Get file to read
	public function GetFile($file, $force_object = false)
	{
		$file = strtolower($file);
		if (!$this->HasFile($file) && !$force_object)
			return null;

		$content = $this->ReadFile($file);

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$reader = ArchiveFile::class;

		// Read a file
		if (array_key_exists($file, $this->file_readers))
		{
			$reader = $this->file_readers[$file];
		}
		// Read a type
		elseif (array_key_exists($ext, $this->filetype_readers))
		{
			$reader = $this->filetype_readers[$ext];
		}
		elseif (!$force_object)
		{
			return $content;
		}
		
		$reader = new $reader($this->archive_name, $file);
		$reader->Set($content);
		return $reader;
	}

	// Get file to read, without object
	public function GetFileRaw($file)
	{
		$file = strtolower($file);
		if (!$this->HasFile($file))
			return false;

		return $this->ReadFile($file);
	}

	// Set file to archive
	public function SetFile($obj)
	{
		if ($obj instanceof ArchiveFile)
		{
			// Remove old file
			if ($obj->Filename() != $obj->PreviousFilename())
				$this->RemoveFile($obj->PreviousFilename());

			$this->WriteFile($obj->Filename(), $obj->Get());
		}
	}

	// Validates file to contain the required data
	public function Validate()
	{
		return true;
	}

	// Get out all those pesky files that somehow get into every other add-on
	public function Cleanup()
	{
		$this->RemoveFile('Thumbs.db'); // Windows thumbnails
		$this->RemoveFile('.DS_Store'); // Mac folder attributes
		$this->RemoveFolder('__MACOSX'); // Mac folder
		$this->RemoveFolder('.svn'); // SVN
		$this->RemoveFolder('.git'); // GIT
		$this->RemoveFile('.gitignore'); // GIT ignore
	}

	// Remove all files with this name
	protected function RemoveFile($file)
	{
		$found = false;
		while (($index = $this->archive->locateName($file, \ZipArchive::FL_NODIR | \ZipArchive::FL_NOCASE)) !== false)
			$found |= $this->archive->deleteIndex($index);
		$this->CountFileTypes();
		return $found;
	}

	// Remove folder and everything in it
	protected function RemoveFolder($folder)
	{
		if (!$this->HasFolder($folder))
			return;
		$len = strlen($folder);
		$found = false;
		for ($i = 0; $i < $this->archive->numFiles; $i++)
			if (substr($this->archive->getNameIndex($i), 0, $len) == $folder)
				$found |= $this->archive->deleteIndex($i);
		$this->CountFileTypes();
		return $found;
	}

	protected function HasFile($file)
	{
		return $this->archive->locateName($file, \ZipArchive::FL_NOCASE | \ZipArchive::FL_NODIR) !== false;
	}

	protected function HasFolder($folder)
	{
		if (substr($folder, -1) != '/')
			$folder .= '/';
		return !$this->HasFile($folder) && $this->archive->locateName($folder, \ZipArchive::FL_NOCASE) !== false;
	}

	protected function HasFileType($ext)
	{
		$ext = strtolower($ext);
		return isset($this->filetype_count[$ext]) && $this->filetype_count[$ext] > 0;
	}

	protected function ReadFile($file)
	{
		return $this->archive->getFromName($file, 0, \ZipArchive::FL_NOCASE);
	}

	// Write to file if not empty
	protected function WriteFile($file, $content)
	{
		if ($content !== null && !empty($content))
		{
			$this->archive->addFromString($file, $content);
			$this->CountFileTypes();
		}
	}

	// Count all file types
	private function CountFileTypes()
	{
		$this->filetype_count = [];
		for ($i = 0; $i < $this->archive->numFiles; $i++)
		{
			$stat = $this->archive->statIndex($i);
			$values = explode('.', $stat['name']);
			$ext = strtolower(end($values));

			if (!isset($this->filetype_count[$ext]))
				$this->filetype_count[$ext] = 0;

			++$this->filetype_count[$ext];
		}
	}
}

?>