import json
import os

profile_data_path = r"d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\profile_data.txt"
vscode_dir = r"d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\.vscode"

if not os.path.exists(vscode_dir):
    os.makedirs(vscode_dir)

extensions = []
settings_lines = []
reading_extensions = False
reading_settings = False

with open(profile_data_path, 'r', encoding='utf-8') as f:
    for line in f:
        line = line.strip()
        if line == "EXTENSIONS_START":
            reading_extensions = True
            continue
        elif line == "EXTENSIONS_END":
            reading_extensions = False
            continue
        elif line == "SETTINGS_START":
            reading_settings = True
            continue
        elif line == "SETTINGS_END":
            reading_settings = False
            continue
        
        if reading_extensions:
            if line:
                extensions.append(line)
        elif reading_settings:
            settings_lines.append(line)

# Create extensions.json
extensions_json_path = os.path.join(vscode_dir, "extensions.json")
extensions_data = {
    "recommendations": extensions
}
with open(extensions_json_path, 'w', encoding='utf-8') as f:
    json.dump(extensions_data, f, indent=4)

# Create settings.json
settings_json_path = os.path.join(vscode_dir, "settings.json")
settings_content = "\n".join(settings_lines)
try:
    # Validate JSON
    settings_obj = json.loads(settings_content)
    with open(settings_json_path, 'w', encoding='utf-8') as f:
        json.dump(settings_obj, f, indent=4)
except json.JSONDecodeError as e:
    print(f"Error decoding settings JSON: {e}")

# Create install script
install_script_path = r"d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\install_extensions.ps1"
with open(install_script_path, 'w', encoding='utf-8') as f:
    f.write("$extensions = @(\n")
    # Join with commas, but ensure the last one doesn't have one if needed, 
    # though PowerShell usually allows trailing comma in multi-line arrays if formatted correctly, 
    # but the previous error suggested otherwise. 
    # Let's just write them cleanly.
    for i, ext in enumerate(extensions):
        if i < len(extensions) - 1:
            f.write(f'    "{ext}",\n')
        else:
            f.write(f'    "{ext}"\n')
    f.write(")\n\n")
    f.write("foreach ($ext in $extensions) {\n")
    f.write("    Write-Host \"Installing $ext...\"\n")
    f.write("    code --install-extension $ext --force\n")
    f.write("}\n")
