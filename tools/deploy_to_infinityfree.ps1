param(
    [string]$HostName = "ftpupload.net",
    [string]$UserName = "if0_41736380",
    [string]$RemoteRoot = "/htdocs",
    [string]$FileList = "tools/deploy_files.txt"
)

$ErrorActionPreference = "Stop"

function ConvertTo-PlainText($secureString) {
    $ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secureString)
    try {
        return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr)
    } finally {
        [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr)
    }
}

function ConvertTo-FtpPath($path) {
    return ($path -replace "\\", "/").TrimStart("/")
}

function ConvertTo-FtpUrlPath($path) {
    $parts = (ConvertTo-FtpPath $path).Split("/")
    return ($parts | ForEach-Object { [Uri]::EscapeDataString($_) }) -join "/"
}

$root = Resolve-Path -LiteralPath (Join-Path $PSScriptRoot "..")
$listPath = Join-Path $root $FileList
if (!(Test-Path -LiteralPath $listPath)) {
    throw "File list not found: $FileList"
}

$password = ConvertTo-PlainText (Read-Host "FTP password" -AsSecureString)
$files = Get-Content -LiteralPath $listPath | Where-Object {
    $line = $_.Trim()
    $line -ne "" -and !$line.StartsWith("#")
}

if ($files.Count -eq 0) {
    throw "No files listed in $FileList"
}

foreach ($relative in $files) {
    $localPath = Join-Path $root $relative
    if (!(Test-Path -LiteralPath $localPath -PathType Leaf)) {
        throw "Local file not found: $relative"
    }

    $remotePath = ConvertTo-FtpUrlPath $relative
    $remoteUrl = "ftp://$HostName$RemoteRoot/$remotePath"
    Write-Host "Uploading $relative"
    & curl.exe --fail --ftp-create-dirs --user "$UserName`:$password" --upload-file "$localPath" "$remoteUrl"
    if ($LASTEXITCODE -ne 0) {
        throw "Upload failed: $relative"
    }
}

Write-Host "Upload completed."
