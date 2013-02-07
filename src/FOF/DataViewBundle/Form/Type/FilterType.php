<?php

namespace FOF\DataViewBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Filter form type
 *
 * @package DataViewBundle
 * @subpackage Form
 * @author George Zankevich <george.zankevich.fof@gmail.com> 
 */
class FilterType extends AbstractType
{
    private $columns = array();

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('column_name', 'choice', array(
                'required' => true,
                'choices' => $this->columns,
            ))
            ->add('comparison_type', 'choice', array(
                'required' => true,
                'choices' => array('=' => '=', '<' => '<', '>' => '>'),
            ))
            ->add('compare_value', 'text', array(
                'required' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'fof_dataviewbundle_filtertype';
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'DataView\Filter',
		));
	}

}

