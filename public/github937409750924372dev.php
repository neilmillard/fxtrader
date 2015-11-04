<?php
/**
 * GIT DEPLOYMENT SCRIPT
 *
 * Used for automatically deploying websites via github or bitbucket, more deets here:
 *
 *        https://gist.github.com/1809044
 */

// The commands
$commands = array(
    'echo $PWD',
    'whoami',
    'putenv("HOME=/home/neilmillard/")',
    'cd /home/neilmillard/webapps/fxtrader_app',
    'git status',
    'git pull origin dev',
);

// Run the commands for output
$output = '';
$tmp = shell_exec('whoami');
if ($tmp == 'neilmillard') {
    foreach ($commands AS $command) {
        // Run it
        $tmp = shell_exec($command);
        // Output
        $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
        $output .= htmlentities(trim($tmp)) . "\n";
    }
} else {
    $output = "Wrong host";
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
[| <span style="color: #FF0000;">&hearts; &hearts;</span> |]  | Git Deployment Script v0.1 |
 |___==___|  / &copy; <a href="https://gist.github.com/oodavid">oodavid</a> 2012 |
              |____________________________|

    <?php echo $output; ?>
</pre>
</body>
</html>