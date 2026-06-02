$getResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -UseBasicParsing -SessionVariable sess
$token = $getResponse.Content -replace '(?s).*name="_token"[^>]*value="([^"]*)".*', '$1'

$postBody = @{email="sidi@sidi-agri.sn"; password="client123"; _token=$token}
$postResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/login-client" -Method POST -Body $postBody -WebSession $sess -UseBasicParsing

# Extraire les erreurs si présentes
if ($postResponse.Content -match '<div class="form-feedback error">(.*?)</div>') {
  Write-Host "=== ERREURS TROUVÉES ==="
  Write-Host $matches[1]
}

# Extraire le titre de la page
if ($postResponse.Content -match '<title>([^<]*)</title>') {
  Write-Host "Title: $($matches[1])"
}

# Vérifier si elle contient dashboard
if ($postResponse.Content -match 'SeneBI - Espace client') {
  Write-Host "Page is: Client Dashboard (Success!)"
  # Extraire un morceau pour montrer
  if ($postResponse.Content -match '<h1[^>]*>([^<]*)</h1>') {
    Write-Host "H1: $($matches[1])"
  }
} elseif ($postResponse.Content -match 'SeneBI:.*Connexion') {
  Write-Host "Page is: Login page (Failed!)"
} else {
  Write-Host "Page is: Unknown"
}
