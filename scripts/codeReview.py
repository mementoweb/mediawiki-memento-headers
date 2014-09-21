#!/usr/bin/python

import sys
import requests
import json

inputfile = sys.argv[1]

if len(sys.argv) == 3:
    outputfile = sys.argv[2]
else:
    outputfile = inputfile

print "Processing file " + inputfile

f = open(inputfile)
inputCode = f.read()
f.close()

apiURI = 'http://tools.wmflabs.org/stylize/jsonapi.php?action=stylizephp'

payload = { 'code' : inputCode }

r = requests.post(apiURI, data=payload)

j = json.loads(r.text)

g = open(outputfile, 'w')
g.write(j['stylizephp'])
g.close()
