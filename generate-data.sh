#!/bin/bash

spinner() {
    local pid=$!
    local delay=0.1
    local spinstr='|/-\'
    while [ "$(ps a | awk '{print $1}' | grep $pid)" ]; do
        local temp=${spinstr#?}
        printf " [%c]  " "$spinstr"
        local spinstr=$temp${spinstr%"$temp"}
        sleep $delay
        printf "\b\b\b\b\b\b"
    done
    printf "    \b\b\b\b"
}

START_DATE="2022-01-01"
END_DATE="2023-12-31"

echo "Importing merchants..."
php artisan sequra:import-merchants & spinner
echo "OK"

echo "Importing orders..."
php artisan sequra:import-orders & spinner
echo "OK"

start=$(date -d $START_DATE +%s)
end=$(date -d $END_DATE +%s)
current=$start

echo "Generating disbursements for 2022 and 2023..."
while [ $current -le $end ]; do
    date=$(date -d @$current +%Y-%m-%d)
    echo -n "Processing $date..."
    php artisan sequra:generate-disbursements $date &
    pid=$!
    spinner
    wait $pid
    echo " Done"
    current=$(( $current + 86400 ))
done

echo "Data generation completed.\n"

php artisan sequra:summary
