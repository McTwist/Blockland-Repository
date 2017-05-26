<?php
/**
 * Creates datalist element with options per definition of HTML5
 */
Form::macro('datalist', function ($name, $list = [], $key_value = false, $options = [])
{
	$options['id'] = $this->getIdAttribute($name, $options);

	if (!isset($options['id']))
	{
		$options['id'] = $name;
	}

	$html = [];

	foreach ($list as $value => $display)
	{
		$html[] = $this->option($display, ($key_value ? $value : null), false);
	}

    $options = $this->html->attributes($options);

    $list = implode('', $html);

    return $this->toHtmlString("<datalist{$options}>{$list}</datalist>");
});
