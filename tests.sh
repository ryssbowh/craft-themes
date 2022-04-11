#!/bin/bash

CODE=0;
STOPONERROR=1;
DEBUG='';

red=`tput setaf 1`
green=`tput setaf 2`
magenta=`tput setaf 5`
bold=`tput bold`
reset=`tput sgr0`

PHP=("8.0" "8.1")
ALLPHP=("8.0" "8.1")
DB=("postgres" "mysql")
ALLDB=("postgres" "mysql")
CRAFT=("3.7 4.0.0-beta.1")
ALLCRAFT=("3.7 4.0.0-beta.1")
USAGE="
./tests.sh [--craft versions] [--db databases] [--php versions] [--stop] [--debug]: Run unit tests for some craft version, some php version, and different database. You will need php 7.3 7.4 8.0 and 8.1, mysql (localhost, user: root, password: root) and postgresql (localhost, user: postgres, password: root) installed on your system for this commands to work.\n
Options :\n
    Craft versions : comma separated, valid values : 3.7 4.0.0-beta.1\n
    PHP versions : comma separated, valid values : 8.0 8.1\n
    databases : comma separated, valid values : mysql postgres\n
    stop : Stop on any error
    debug : Enable codeception debug mode"

i=1;
j=$#;
while [ $i -le $j ]; do
    if [ "$1" = "--help" ]; then
        echo -e $USAGE;
        exit 0;
    fi
    if [ "$1" = "--stop" ]; then
        shift 1;
        STOPONERROR=$1;
    elif [ "$1" = "--debug" ]; then
        shift 1;
        DEBUG=--debug;
    elif [ "$1" = "--php" ]; then
        shift 1;
        IFS="," read -a PHP <<< $1
        IFS=" "
        for php in ${PHP[@]}; do
            if [[ ! " ${ALLPHP[*]} " =~ " ${php} " ]]; then
                echo "${red}$php is not a valid php version${reset}"
                exit 1;
            fi
        done
    elif [ "$1" = "--craft" ]; then
        shift 1;
        IFS="," read -a CRAFT <<< $1
        IFS=" "
        for craft in ${CRAFT[@]}; do
            if [[ ! " ${ALLCRAFT[*]} " =~ " ${craft} " ]]; then
                echo "${red}$craft is not a valid craft version${reset}"
                exit 1;
            fi
        done
    elif [ "$1" = "--db" ]; then
        shift 1;
        IFS="," read -a DB <<< $1
        IFS=" "
        for db in ${DB[@]}; do
            if [[ ! " ${ALLDB[*]} " =~ " ${db} " ]]; then
                echo "${red}$db is not a valid database${reset}"
                exit 1;
            fi
        done
    fi
    shift 1;
    i=$((i + 1));
done

for php in ${PHP[@]}; do
    for craft in ${CRAFT[@]}; do
        php$php $(which composer) require craftcms/cms:"^$craft" craftcms/commerce:^4.0.0-beta.1 -W -o
        code=$?
        CODE=$((CODE + $code));
        if [ "$code" -ne "0" ]; then
            if [ "$STOPONERROR" -eq "1" ]; then
                break 2;
            fi
        fi
        for db in ${DB[@]}; do
            cp "tests/.env.$db" tests/.env
            echo "
${magenta}${bold}------------- Testing with $db/Craft $craft/php$php -----------${reset}
"
            php$php vendor/bin/codecept run unit $DEBUG
            code=$?
            CODE=$((CODE + $code));
            if [ "$code" -ne "0" ]; then
                if [ "$STOPONERROR" -eq "1" ]; then
                    break 3;
                fi
            fi
        done
    done
done

echo "
";

if [ "$CODE" -eq "0" ]; then
    echo "${green}${bold}All tests passed successfully${reset}";
else
    echo "${red}${bold}Some tests failed, code $CODE${reset}" 1>&2;
fi

#Reset composer.json
echo "Reverting composer...";
composer remove craftcms/commerce -W --ignore-platform-reqs --quiet;
composer require craftcms/cms:"^4.0.0-beta.1" -W --ignore-platform-reqs --quiet;
rm composer.lock;

exit $CODE;