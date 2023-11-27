param (
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [Parameter(Mandatory=$true)]
    [string]$OrgName,
    [bool]$DryRun = $false
)

Write-Output "Downloading latest javascript-templates..."
Invoke-WebRequest "https://github.com/$OrgName/javascript-templates/archive/refs/heads/main.zip" -OutFile javascript-templates.zip

Write-Output "Extracting the archive..."
Expand-Archive javascript-templates.zip -DestinationPath .

Write-Output "Updating the package directory..."
Move-Item -Path javascript-templates-main/*.mustache -Destination $RepoName/javascript-templates -Force

Write-Output "Cleaning up..."
Remove-Item javascript-templates.zip, javascript-templates-main -Recurse
