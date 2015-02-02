#!/bin/bash
#This script deletes current SDAPs project and recreate it.
rm -r pmccs_aundh
sdaps /var/www/html/pmccs_aundh setup_tex /var/www/html/test.tex
echo "Success"
