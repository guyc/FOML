<?php
require_once "../FomlConfig.php";

$defaultUser = 'apache';

// debugging preformatted recursive dump
function Dump($Var)
{
    print "<pre>";
    print_r($Var);
    print "</pre>";
}

// super minimal API for accessing Github API
class GitHub
{
    const API_URL = 'https://api.github.com';

    function Get($Method)
    {
        try {
            $this->body = file_get_contents(self::API_URL.$Method);
        } catch (Exception $ex) {
            print $ex;
            $this->body = null;
        }
    }

    function Body()
    {
        return $this->body;
    }

    function GetRepos($User)
    {
        $this->Get("/users/{$User}/repos");
        if ($this->body) {
            $obj = json_decode($this->body);
            return $obj;
        } else {
            return null;
        }
    }
}

function RenderForm($User, $Message=null)
{
    $encodedUser = htmlspecialchars($User);
    ?>
<form action="" method="GET">
  <input type="text" name="user" value="<?php print $encodedUser;?>"/>
  <input type="submit">
</form>
<?php
}

if (isset($_GET['user'])) {
    $user = $_GET['user'];
    $github = new GitHub();
    $repos = $github->GetRepos($user);
    if (!$repos) {
        $encodedUser = htmlspecialchars($user);
        RenderForm($user, "No repositories found for {$encodedUser}");
    } else {
        Foml::RenderAttachment("foml/Repositories.foml", "github-{$user}.pdf", array('repos'=>$repos));
    }
} else {
    RenderForm($defaultUser);
}

?>