<?php
	function validate_email($email) {
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return 1;
		}
		else {
			return "* Invalid Email (Example: name@example.com)";
		}
	}
	
	function validate_name($name) {
		// Allow English letters, Bengali characters, and spaces
		if(preg_match("/^[\p{L}\p{M}\s]{2,50}$/u",$name)) {
			return 1;
		}
		else {
			return "* Invalid Name (2-50 characters, English/Bengali letters only)";
		}
	}
	
	function validate_password($password) {
		if(strlen($password) > 4 && strlen($password) < 31) {
			return 1;
		}
		else {
			return "* Password must be 5-30 characters";
		}
	}
	
	function validate_phone($phone) {
		// Bangladesh phone number validation
		// Mobile: 11 digits starting with 01 (e.g., 01712345678)
		// Landline: Area code + number (2-4 digit area code + 6-8 digit number)
		if(preg_match("/^01[3-9][0-9]{8}$/",$phone)) {
			return 1; // Mobile numbers: 01X-XXXX-XXXX (where X is 3-9 for second digit)
		}
		elseif(preg_match("/^0[2-9][0-9]{6,9}$/",$phone)) {
			return 1; // Landline: 0XX-XXXXXX to 0XXXX-XXXXXXXX
		}
		else {
			return "* Invalid Phone (Mobile: 01XXXXXXXXX, Landline: 0XX-XXXXXX)";
		}
	}
	
	function validate_postal_code($postal_code) {
		// Bangladesh postal codes are 4 digits
		if(preg_match("/^[0-9]{4}$/",$postal_code)) {
			return 1;
		}
		else {
			return "* Invalid Postal Code (4 digits, e.g., 1000 for Dhaka)";
		}
	}
	
	function validate_number($number) {
		if(preg_match("/^[0-9]*$/",$number)) {
			return 1;
		}
		else {
			return "* Invalid number (digits only)";
		}
	}
	
	function validate_price($price) {
		// Allow prices in Taka format (can include decimal points)
		if(preg_match("/^[0-9]+(\.[0-9]{1,2})?$/",$price) && $price > 0) {
			return 1;
		}
		else {
			return "* Invalid Price (Taka amount, e.g., 100.50)";
		}
	}
	
	function validate_username($username) {
		// Allow alphanumeric and underscore, suitable for Bangladesh users
		if(preg_match("/^[a-zA-Z0-9_]{3,20}$/",$username)) {
			return 1;
		}
		else {
			return "* Username: 3-20 characters (letters, numbers, underscore only)";
		}
	}
	
	function validate_nid($nid) {
		// Bangladesh National ID validation (10, 13, or 17 digits)
		if(preg_match("/^[0-9]{10}$/",$nid) || preg_match("/^[0-9]{13}$/",$nid) || preg_match("/^[0-9]{17}$/",$nid)) {
			return 1;
		}
		else {
			return "* Invalid NID (10, 13, or 17 digits)";
		}
	}
	
	function validate_trade_license($license) {
		// Bangladesh trade license format (varies by area, generally alphanumeric)
		if(preg_match("/^[A-Z0-9\-\/]{5,20}$/",$license)) {
			return 1;
		}
		else {
			return "* Invalid Trade License (5-20 characters, letters/numbers/dash/slash)";
		}
	}
?>