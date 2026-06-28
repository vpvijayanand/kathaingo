# ============================================================
# Kathaingo Database Backup Script
# Run this manually or schedule it via Windows Task Scheduler
# ============================================================

$ProjectRoot = "C:\Users\ilang\OneDrive\Desktop\Kathaingo\kathaingo"
$BackupDir   = "$ProjectRoot\storage\backups\database"
$LogFile     = "$BackupDir\backup.log"
$MaxBackups  = 30   # Keep last 30 daily backups

# --- Database credentials (reads from .env) ---
$EnvPath = "$ProjectRoot\.env"
$DbName  = "kathaingo"
$DbUser  = "ilang"
$DbHost  = "127.0.0.1"
$DbPort  = "5432"

# Create backup directory if it doesn't exist
if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null
}

# Generate timestamped filename
$Timestamp  = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$BackupFile = "$BackupDir\kathaingo_$Timestamp.sql"

# --- Run pg_dump ---
Write-Host "[$Timestamp] Starting database backup..."
$Env:PGPASSWORD = ""   # Set if you have a password, e.g. $Env:PGPASSWORD = "yourpassword"

try {
    pg_dump -U $DbUser -h $DbHost -p $DbPort -d $DbName -F p -f $BackupFile 2>&1
    if ($LASTEXITCODE -eq 0) {
        $SizeKB = [math]::Round((Get-Item $BackupFile).Length / 1024, 1)
        $Msg = "[$Timestamp] SUCCESS: Backed up to $BackupFile ($SizeKB KB)"
        Write-Host $Msg -ForegroundColor Green
        Add-Content -Path $LogFile -Value $Msg
    } else {
        $Msg = "[$Timestamp] ERROR: pg_dump failed with exit code $LASTEXITCODE"
        Write-Host $Msg -ForegroundColor Red
        Add-Content -Path $LogFile -Value $Msg
        exit 1
    }
} catch {
    $Msg = "[$Timestamp] EXCEPTION: $($_.Exception.Message)"
    Write-Host $Msg -ForegroundColor Red
    Add-Content -Path $LogFile -Value $Msg
    exit 1
}

# --- Prune old backups (keep last $MaxBackups) ---
$AllBackups = Get-ChildItem -Path $BackupDir -Filter "kathaingo_*.sql" |
              Sort-Object LastWriteTime -Descending

if ($AllBackups.Count -gt $MaxBackups) {
    $ToDelete = $AllBackups | Select-Object -Skip $MaxBackups
    foreach ($File in $ToDelete) {
        Remove-Item $File.FullName -Force
        $PruneMsg = "[$Timestamp] PRUNED old backup: $($File.Name)"
        Write-Host $PruneMsg -ForegroundColor Yellow
        Add-Content -Path $LogFile -Value $PruneMsg
    }
}

Write-Host "Backup complete. Backups stored in: $BackupDir"
