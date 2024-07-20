#!/bin/bash

# Variables
OLD_MODULE_NAME="bootstrap_carousel_toolbox"
NEW_MODULE_NAME="bootstrap_toolbox_carousel"

# Function to replace strings in files
replace_in_file() {
  local file=$1
  local search=$2
  local replace=$3
  sed -i "s/${search}/${replace}/g" "$file"
}

# Function to rename directories and files
rename_files() {
  local old_dir=$1
  local new_dir=$2

  # Create new directory structure
  mkdir -p "$new_dir"

  # Loop through old directory
  for file in "$old_dir"/*; do
    local new_file
    if [[ -d "$file" ]]; then
      new_file=$(basename "$file")
      rename_files "$file" "$new_dir/$new_file"
    else
      new_file=$(basename "$file" | sed "s/$OLD_MODULE_NAME/$NEW_MODULE_NAME/g")
      cp "$file" "$new_dir/$new_file"
      replace_in_file "$new_dir/$new_file" "$OLD_MODULE_NAME" "$NEW_MODULE_NAME"
      replace_in_file "$new_dir/$new_file" "$(echo $OLD_MODULE_NAME | sed 's/_/ /g' | awk '{for(i=1;i<=NF;i++) $i=toupper(substr($i,1,1)) substr($i,2)}1' | sed 's/ //g')" "$(echo $NEW_MODULE_NAME | sed 's/_/ /g' | awk '{for(i=1;i<=NF;i++) $i=toupper(substr($i,1,1)) substr($i,2)}1' | sed 's/ //g')"
      replace_in_file "$new_dir/$new_file" "$(echo $OLD_MODULE_NAME | sed 's/_/ /g' | awk '{for(i=1;i<=NF;i++) $i=toupper(substr($i,1,1)) substr($i,2)}1' | sed 's/ /_/g')" "$(echo $NEW_MODULE_NAME | sed 's/_/ /g' | awk '{for(i=1;i<=NF;i++) $i=toupper(substr($i,1,1)) substr($i,2)}1' | sed 's/ /_/g')"
    fi
  done
}

# Check if the old module directory exists
if [ ! -d "$OLD_MODULE_NAME" ]; then
  echo "The module $OLD_MODULE_NAME does not exist."
  exit 1
fi

# Check if the new module directory already exists
if [ -d "$NEW_MODULE_NAME" ]; then
  echo "The module $NEW_MODULE_NAME already exists."
  exit 1
fi

# Rename directories and files
rename_files "$OLD_MODULE_NAME" "$NEW_MODULE_NAME"

echo "Module $OLD_MODULE_NAME has been successfully duplicated and renamed to $NEW_MODULE_NAME."

exit 0
