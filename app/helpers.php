<?php

function obfuscate_email($email) {
	return preg_replace('/(?<=..).(?=.*@)/u','*', $email);
}
