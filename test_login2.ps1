$getResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -UseBasicParsing -SessionVariable sess
$token = $getResponse.Content -replace '(?s).*name="_token"[^>]*value="([^"]*)".*', '$1'

$postBody = @{email="sidi@sidi-agri.sn"; password="client123"; _token=$token}
$postResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -Method POST -Body $postBody -WebSession $sess -UseBasicParsing

Write-Host "Status: $($postResponse.StatusCode)"
Write-Host "URL: $($postResponse.BaseResponse.RequestMessage.RequestUri)"
Write-Host "Content length: $($postResponse.Content.Length)"

# Premier 500 caractères
$content = $postResponse.Content.Substring(0, [Math]::Min(800, $postResponse.Content.Length))
Write-Host "Content preview:"
Write-Host $content

