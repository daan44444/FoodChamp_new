<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedin()) {
    Redirect::to('index.php');
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'password_current' => array(
                'required' => true,
                'min' => Config::get('validation/password_min')
            ),
            'password_new' => array(
                'required' => true,
                'min' => Config::get('validation/password_min')
            ),
            'password_new_again' => array(
                'required' => true,
                'matches' => 'password_new'
            )
        ));

        if($validation->passed()) {

            if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password){

            } else {
                $salt = Hash::salt(32);
                $user->update(array(
                    'password' => Hash::make(Input::get('password'), $salt),
                    'salt' => $salt
                ));

                Session::flash('home', 'Password changed!');
                Redirect::to('index.php');
            }

        } else {
            foreach ($validation->errors() as $error) {
                echo $error, '<br/>';
            }
        }

    }
}
?>

<form action="" method="post">
    <div class="field">
        <label for="password_current">Current password</label>
        <input type="password" name="password_current" id="password_current">
    </div>

    <div class="field">
        <label for="password_new">New password</label>
        <input type="password" name="password_new" id="password_new">
    </div>

    <div class="field">
        <label for="password_new_again">New password again</label>
        <input type="password" name="password_new_again" id="password_new_again">
    </div>

    <input type="submit" value="Change">
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
</form>
