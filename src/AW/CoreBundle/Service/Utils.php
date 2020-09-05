<?php

namespace AW\CoreBundle\Service;

class Utils
{
  public function filenameCounter($filename)
  {
    return preg_replace_callback(
      '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
      function($matches){
        $index = isset($matches[1]) ? ((int)$matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return ' ('.$index.')'.$ext;
      },
      $filename,
      1
    );
  }

  public function isDayOff(\DateTime $date)
  {
    if($date->format('N') == 6 or $date->format('N') == 7){ // week end
      return true;
    }

    if($date->format('d-m') == '01-01'){ // nouvel an
      return true;
    }

    if($date->format('d-m') == '01-05'){ // fête de travail
      return true;
    }

    if($date->format('d-m') == '08-05'){ // 8 mai
      return true;
    }

    if($date->format('d-m') == '14-07'){ // fête de national
      return true;
    }

    if($date->format('d-m') == '15-08'){ // assomption
      return true;
    }

    if($date->format('d-m') == '01-11'){ // toussaint
      return true;
    }

    if($date->format('d-m') == '11-11'){ // armistice
      return true;
    }

    if($date->format('d-m') == '25-12'){ // noël
      return true;
    }

    // calcul des paques http://elliptips.info/recuperer-les-jours-feries-en-php/
    $y = $date->format('Y');
    $easterDate = easter_date($y);
    $easterDateTime = new \DateTime('@'.$easterDate);

    $easterDateTime->modify('+2 day'); // lundi de paque
    if($date->format('d-m') == $easterDateTime->format('d-m')){
      return true;
    }

    $easterDateTime->modify('+38 day'); // jeudi de l'ascension
    if($date->format('d-m') == $easterDateTime->format('d-m')){
      return true;
    }

    $date->modify('+11 day'); // lundi de pentecôte
    if($date->format('d-m') == $easterDateTime->format('d-m')){
      return true;
    }

    return false;
  }
}
