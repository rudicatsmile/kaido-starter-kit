import json
import os
import re

file_path = r"d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\my-visual-studio-profile.code-profile"

def remove_comments(json_str):
    # Remove // comments
    json_str = re.sub(r'//.*', '', json_str)
    # Remove /* */ comments
    json_str = re.sub(r'/\*.*?\*/', '', json_str, flags=re.DOTALL)
    return json_str

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    data = json.load(open(file_path, 'r', encoding='utf-8'))

    # Extract extensions
    extensions_str = data.get('extensions', '[]')
    extensions = json.loads(extensions_str)
    extension_ids = [ext['identifier']['id'] for ext in extensions]

    # Extract settings
    settings_str = data.get('settings', '{}')
    
    # First load
    settings_layer_1 = json.loads(settings_str)
    
    # Second load
    if 'settings' in settings_layer_1 and isinstance(settings_layer_1['settings'], str):
        raw_settings = settings_layer_1['settings']
        # Remove comments from the inner JSON string
        clean_settings = remove_comments(raw_settings)
        final_settings = json.loads(clean_settings)
    else:
        final_settings = settings_layer_1

    print("EXTENSIONS_START")
    for ext_id in extension_ids:
        print(ext_id)
    print("EXTENSIONS_END")

    print("SETTINGS_START")
    print(json.dumps(final_settings, indent=2))
    print("SETTINGS_END")

except Exception as e:
    print(f"Error: {e}")
