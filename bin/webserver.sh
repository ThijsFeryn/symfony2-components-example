#!/bin/bash
php -d "date.timezone=Europe/Brussels" -S 0:8888 -t ../web/ ../web/index.php
