<?php
// destination
$gitDir = "/home/neilmillard/webapps/fxtrader_app/fxtrader";

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/loadsettings.php';
$settings = $settings['settings']['logger'];
$logger = new \Monolog\Logger($settings['name']);
$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
if (empty($lsettings['loggly'])) {
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
} else {
    $logger->pushHandler(new \Monolog\Handler\LogglyHandler($settings['loggly'] . '/tag/monolog', \Monolog\Logger::INFO));
}

// Run the commands for output
$output = '';
$event = @$_SERVER['HTTP_X_GITHUB_EVENT'];
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);
$remote = NULL;
$tmp = trim(shell_exec('whoami'));
$ref = $data['ref'];
if ($tmp == 'neilmillard' && $ref == 'refs/heads/dev') {
    exec("git --work-tree={$gitDir} stash", $gitOutput1);
    exec("git --work-tree={$gitDir} pull -f {$remote}", $gitOutput);
    exec("composer -q -n -w=${gitDir} update", $composerOutput);
    exec("rm ${gitDir}/composer.lock");
//    foreach ($commands AS $command) {
//        // Run it
//        $tmp = shell_exec($command);
//        // Output
//        $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
//        $output .= htmlentities(trim($tmp)) . "\n";
//    }
} else {
    $logger->addNotice("post hook error:username:$tmp:ref:$ref");
}

// Make it pretty for manual user access (and why not?)
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
 .  ____  .    ____________________________
 |/      \|   |                            |
[|  <span style="color: #FF0000;">&hearts; &nbsp;&hearts; </span> |]  | Git Deployment Script v0.1 |
 |___==___|  &nbsp;/ &copy; <a href="https://gist.github.com/oodavid">oodavid</a> 2012             |
              |____________________________|
    <?php echo "\r" . $output; ?>
</pre>
</body>
</html>