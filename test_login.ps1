$getResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -UseBasicParsing -SessionVariable sess
$token = $getResponse.Content -replace '(?s).*name="_token"[^>]*value="([^"]*)".*', '$1'
Write-Host "Token: $token"

$postBody = @{email="sidi@sidi-agri.sn"; password="client123"; _token=$token}
try {
  $postResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -Method POST -Body $postBody -WebSession $sess -UseBasicParsing
  Write-Host "Final Status: $($postResponse.StatusCode)"
  Write-Host "Final URL: $($postResponse.BaseResponse.RequestMessage.RequestUri)"
  Write-Host "Page contains client-dashboard: $($postResponse.Content -contains 'client-dashboard')"
} catch {
  Write-Host "Erreur: $_"
}
