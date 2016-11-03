"use strict";       // more secure.

var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([com\co\.\in])+$/; // regex to validate email
var genericNameRegex = /^[a-zA-Z',.\s]*/;
var loginRegex = /^[a-zA-Z0-9_]*/;
var landlineRegex = /^0[0-9]{7,9}/;
var cellPhoneRegex = /^0[0-9]{8,10}/;
var addressRegex = /^[0-9a-zA-Z]+\s[a-zA-z\s]+/;


// validation functions

// generic validation via regex.
function validate(string, regex)
{
	if (!string.match(regex)) {
        return false;
    }

    return true;
}


// Validate a string representing a name.
function ValidateGenericNameString(string) {

    return validate(string, genericNameRegex);
}

// Validate a string representing a login.
function ValidateLoginString(string) {

    return validate(string, loginRegex);
}

// Validate a string representing an Email
function ValidateEmail(string) {

    return validate(string, emailRegex);
}

// Validate a string representing a Mobile contact Number
function ValidateCellNumber(string) {

    return validate(string, cellPhoneRegex);
}

// Validate a string representing a Landline contact Number
function ValidateLandlineNumber(string) {

    return validate(string, landlineRegex);
}

// Validate a string representing an address
function ValidateAddress(string) {

    return validate(string, addressRegex);
}

// specific validation function for registration / profile update form.
function customerFormValidation() {
	
}
