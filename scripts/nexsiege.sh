#!/bin/sh
echo     siege -i -c$1 -t$2s -f urls.txt 

echo "Creating sitemap:"
curl $4 | sed 's/\<url\>/\<url\>\n/g' | grep 0.5 | sed 's/.*loc>\(.*\)<\/loc.*/\1/g' > urls.txt
curl $4 | sed 's/\<url\>/\<url\>\n/g' | grep 1.0 | sed 's/.*loc>\(.*\)<\/loc.*/\1/g' >>urls.txt
echo "Warming cache:"
siege -i -c50 -t60s -f urls.txt 
echo "Waiting 1 minute for the first test."
sleep 1m
echo "Starting tests:"
for i in `seq 1 $5`;
do
    echo "Running test # $i / $5:"
    siege -i -c$1 -t$2s -f urls.txt 
    echo "Waiting 1 minute for the next test."
    sleep 1m
done
echo "Removing urls.txt:"
cp urls.txt check.txt
rm urls.txt
