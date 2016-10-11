"use strict";       // more secure.

var emailExp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([com\co\.\in])+$/; // regex to validate email
var NumericChars = "0123456789";
var AlphaPeriodCommaApostropheSpace = "abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP., '";
var AlphaChars = "abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP";


// Administration Validation functions.


// Validate a string representing a name.
function ValidateNameString(source, args) {

    var char;
    var textLength = args.Value.length;

    for (var i = 0; i < textLength; i++) {
        char = args.Value[i];
        if (AlphaPeriodCommaApostropheSpace.indexOf(char) === -1) {
            args.IsValid = false;
            return false;
        }
    }

    args.IsValid = true;
    return true;
}

// Validate a string representing a name.
function ValidateAlphanumeric(source, args) {

    var char;
    var textLength = args.Value.length;

    for (var i = 0; i < textLength; i++) {
        char = args.Value[i];
        if (AlphaChars.indexOf(char) === -1) {
            args.IsValid = false;
            return false;
        }
    }

    args.IsValid = true;
    return true;
}

// Validate a string representing an Email
function ValidateEmail(source, args) {

    if (!textContent.match(emailExp)) {
        args.IsValid = false;
        return false;
    }

    args.IsValid = true;
    return true;
}

// Validate a string representing a Mobile contact Number
function ValidateMobileNumber(source, args) {

    if (args.Value[0] !== 0 ||
        args.Value.substring(0,3) !== "+64") {
        args.IsValid = false;
        return false;
    }

    var subNumber;

    if (args.Value.substring(0, 3) === "+64") {
        subNumber = args.Value.substring(3);
    } else {
        subNumber = args.Value.substring(1);
    }

    if (subNumber.length < 7 || subNumber.length > 8) {
        args.IsValid = false;
        return false;
    }

    var char;
    var textLength = subNumber.length;
    var i = 0;

    for (i = 0; i < textLength; i++) {
        char = subNumber[i];
        if (NumericChars.indexOf(char) === -1) {
            args.IsValid = false;
            return false;
        }
    }

    args.IsValid = true;
    return true;

}

// Validate a string representing a Landline contact Number
function ValidateLandlineNumber(source, args) {

    if (args.Value[0] !== 0) {
        args.IsValid = false;
        return false;
    }

    if (args.Value.length < 8 || args.Value.length > 10) {
        args.IsValid = false;
        return false;
    }

    var char;
    var textLength = args.Value.length;
    var i = 1;

    for (i = 1; i < textLength; i++) {
        char = args.Value[i];
        if (NumericChars.indexOf(char) === -1) {
            args.IsValid = false;
            return false;
        }
    }

    args.IsValid = true;
    return true;

}