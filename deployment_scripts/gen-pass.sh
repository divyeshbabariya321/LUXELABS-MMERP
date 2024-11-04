#/bin/bash
echo "iii = $1"
htpasswd -bnBC 8 "" $1 |  grep -oP '\$2[ayb]\$.{56}'
