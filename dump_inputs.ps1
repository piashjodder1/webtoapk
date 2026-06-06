$jsonlPath = "C:\Users\piash\.gemini\antigravity\brain\527cc2ca-c73e-4d12-add4-e5f68acb7bfd\.system_generated\logs\transcript.jsonl"
$outputPath = "D:\web to apps\user_inputs.txt"

Get-Content -Path $jsonlPath | ForEach-Object {
    if ($_ -match '"type":"USER_INPUT"') {
        $obj = $_ | ConvertFrom-Json
        Add-Content -Path $outputPath -Value $obj.content -Encoding UTF8
    }
}
