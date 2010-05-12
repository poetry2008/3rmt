<?php
/*
  $Id$
*/
?>

<script type="text/javascript" language="javascript"><!--

var submitted = false;

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  if (submitted == true) {
    alert("<?php echo JS_ERROR_SUBMITTED; ?>");
    return false;
  }

  var first_name = document.present_account.firstname.value;
  var last_name = document.present_account.lastname.value;

<?php
   if (ACCOUNT_DOB == 'true') echo '  var dob = document.present_account.dob.value;' . "\n";
?>

  var email_address = document.present_account.email_address.value;
  var street_address = document.present_account.street_address.value;
  var postcode = document.present_account.postcode.value;
  var city = document.present_account.city.value;
  var telephone = document.present_account.telephone.value;
  var password = document.present_account.password.value;
  var confirmation = document.present_account.confirmation.value;

<?php
   if (ACCOUNT_GENDER == 'true') {
?>
  if (document.present_account.elements['gender'].type != "hidden") {
    if (document.present_account.gender[0].checked || document.present_account.gender[1].checked) {
    } else {
      error_message = error_message + "<?php echo JS_GENDER; ?>";
      error = 1;
    }
  }
<?php
  }
?>

  if (document.present_account.elements['firstname'].type != "hidden") {
    if (first_name == '' || first_name.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
      error = 1;
    }
  }

  if (document.present_account.elements['lastname'].type != "hidden") {
    if (last_name == '' || last_name.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
      error = 1;
    }
  }

<?php
   if (ACCOUNT_DOB == 'true') {
?>
  if (document.present_account.elements['dob'].type != "hidden") {
    if (dob == '' || dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_DOB; ?>";
      error = 1;
    }
  }
<?php
  }
?>

  if (document.present_account.elements['email_address'].type != "hidden") {
    if (email_address == '' || email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
      error = 1;
    }
  }

  if (document.present_account.elements['street_address'].type != "hidden") {
    if (street_address == '' || street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_ADDRESS; ?>";
      error = 1;
    }
  }

  if (document.present_account.elements['postcode'].type != "hidden") {
    if (postcode == '' || postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_POST_CODE; ?>";
      error = 1;
    }
  }

  if (document.present_account.elements['city'].type != "hidden") {
    if (city == '' || city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_CITY; ?>";
      error = 1;
    }
  }

<?php
  if (ACCOUNT_STATE == 'true') {
?>
  if (document.present_account.elements['state'].type != "hidden") {
    if (document.present_account.state.value == '' || document.present_account.state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  }
<?php
  }
?>

  //if (document.present_account.elements['country'].type != "hidden") {
  //  if (document.present_account.country.value == 0) {
  //    error_message = error_message + "<?php echo JS_COUNTRY; ?>";
  //    error = 1;
  //  }
  //}

  if (document.present_account.elements['telephone'].type != "hidden") {
    if (telephone == '' || telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_TELEPHONE; ?>";
      error = 1;
    }
  }

  //if (document.present_account.elements['password'].type != "hidden") {
  //  if ((password != confirmation) || (password == '' || password.length < <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>)) {
  //    error_message = error_message + "<?php echo JS_PASSWORD; ?>";
  //    error = 1;
  //  }
  //}

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }
}
//--></script>