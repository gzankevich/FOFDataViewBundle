<?php

namespace FOF\DataViewBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * DataView form type
 *
 * @package DataViewBundle
 * @subpackage Form
 * @author George Zankevich <george.zankevich.fof@gmail.com> 
 */
class DataViewType extends AbstractType
{
    private $columns = array();

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filters', 'collection', array(
                'type'          => new FilterType($this->columns),
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'prototype'     => true,
            ))
        ;
    }

    public function getName()
    {
        return 'fof_dataviewbundle_dataviewtype';
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'DataView\DataView',
		));
	}
}

