<?php
// Files in your project
$files = ["Signup.php", "mail.php", "vendor/", "git_workflow.txt"];

// Workflow content
$workflow = "# Git Workflow for BBIT Enterprise Project\n\n";
$workflow .= "### 1. Clone repository\n";
$workflow .= "git clone https://github.com/reechelly/GitAssingment\n\n";
$workflow .= "### 2. Add project files\n";
foreach ($files as $file) {
    $workflow .= "- $file\n";
}
$workflow .= "\n### 3. Stage and commit\n";
$workflow .= "git add .\n";
$workflow .= "git commit -m \"Updated project files\"\n\n";
$workflow .= "### 4. Push changes\n";
$workflow .= "git push origin main\n";

// Save to file
file_put_contents("git_workflow.txt", $workflow);

echo "✅ git_workflow.txt has been created successfully!";