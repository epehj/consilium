<?php

namespace AW\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType as CKEditorBaseType;

class CKEditorType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'config' => array(
        'removePlugins' => 'save,newpage,print,find,image,div,flash,forms,templates,language,pagebreak,table,tableselection,tabletools,specialchar,horizontalrule,iframe,smiley',
        'removeButtons' => 'Anchor'
      )
    ));
  }

  public function getParent()
  {
    return CKEditorBaseType::class;
  }
}
