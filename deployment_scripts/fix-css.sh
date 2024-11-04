#!/bin/bash
EXTENDLESSLIST=`find app/design/ -name _extend.less`
ALLLESSLIST=`find app/design/ -name *.less`
for LESS in $EXTENDLESSLIST
do
	ECODE=`cat $LESS |  grep "^[^@/]"`
	if [ ! -z "$ECODE" ] 
	then
		echo "ERROR = non variable code found in $LESS================================="
		echo "$ECODE"
	fi
	# Check for non-variable code
#	if grep -v '^ *@[a-zA-Z_-]\+:.*;$' "$LESS" | grep -q '[^[:space:]]'; then
#		echo "Error: The Less file contains non-variable code. : $LESS"
#	else
#		echo "The Less file only contains variable declarations. : $LESS"
#	fi
done

for LESS in $ALLLESSLIST
do
       #ECODE=`cat $LESS |  grep $'\t'`
       ECODE=`cat $LESS |  grep -P '\t| {4,}'`
        if [ ! -z "$ECODE" ]
        then
                echo "ERROR = TAB INDENT found in $LESS================================="
                echo "$ECODE"
        fi

#	if grep -P '\t| {4,}' "$LESS"; then
#		echo "Error: Incorrect indentation found. : $LESS"
#	else
#		echo "Indentation is correct. : $LESS"
#	fi

done

for LESS in $ALLLESSLIST
do
	# Find styles outside of media queries
        #ECODE=`grep -Pzo "(?s)(?<=^|})[^@{]*\{[^}]*\}" $LESS`

	# Find styles outside of media queries using Magento's media query variables
        ECODE=`grep -Pzo "(?s)(?<=^|})[^@{]*\{[^}]*\}" "$LESS" | grep -vE "\(@media|@screen\).*\$\w+.*\{"`
        if [ ! -z "$ECODE" ]
        then
                echo "ERROR = style outside media found  $LESS================================="
                echo "$ECODE"
        fi
done

for LESS in $ALLLESSLIST
do
	# Add a newline before each CSS class
#	sed -i 's/\([^{]\)\(\.[a-zA-Z][a-zA-Z0-9_-]*\)/\1\n\2/g' $LESS

	# Remove commented-out syntax
#	sed -i '/\/\*/,/\*\//d' $LESS 

	# Replace font-size=n with .lib-font-size(n)
	sed -i 's/font-size=\([0-9]*\)/.lib-font-size(\1)/g' $LESS

	# Replace display: flex; with .lib-vendor-prefix-display(flex);
	sed -i 's/display:\s*flex;/\.lib-vendor-prefix-display(flex);/g' $LESS
	# Replace line-height: 24px; with .lib-line-height(24);
#	sed -i 's/line-height:\s*24px;/\.lib-line-height(24);/g'  $LESS

	# Find and modify styles inside @media-common = true
	#grep -zoP "@media\s*and\s*\(@media-common\s*=\s*true\s*\)\s*{" "$LESS" |  sed 's/{/\n/g' | sed '/}/!b;N;s/\(.*\)\n\(.*}\)/\2\n\1/;s/{/\n/g' | sed '/@media-common = true/d' > "$LESS"

	# Replace font-weight: 600; with font-weight: $font-weight-semibold;
	#sed 's/font-weight:\s*600;/font-weight: $font-weight-semibold;/g' "$input_file" > "$output_file"

	# Replace font-family: 'Icomoon'; with font-family: var(--icomoon-font-family);
#	sed "s/font-family:\s*'Icomoon';/font-family: var(--icomoon-font-family);/g" "$LESS" |
	# Replace content: '\e123'; with content: var(--icon-123);
#	sed "s/content:\s*'\\e\([0-9a-fA-F]*\)';/content: var(--icon-\1);/g" > "$LESS"
done

