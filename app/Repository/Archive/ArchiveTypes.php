<?php
namespace App\Repository\Archive;

trait ArchiveTypes
{
	private $filetype_readers = [];
	private $file_readers = [];

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

	// Get a file reader depending on file
	// file - The complete file path
	protected function GetFileReader($file)
	{
		$file = strtolower($file);

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
		
		return $reader;
	}
}

?>
