<?php

namespace EasyApiCore\Form\Type;

use EasyApiCore\Util\ApiProblem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiType extends AbstractType
{
    protected static ?string $dataClass = null;

    /**
     * exemple :
     *  [
     *      'name.blank' => ['refType.code in' => ['type_1', 'type_2'], 'refNature.code in' => ['nat_1', 'nat_2']],
     *      'name.blank' => ['refType.code notin' => ['type_3'],
     *  ]
     * meens name is blank if refType is in ['type_1', 'type_2'] AND refNature is in ['nat_1', 'nat_2']
     * OR name is blank if refType is not in ['type_3'].
     *
     * @var array
     */
    protected static array $groupsConditions = [];

    protected array $validationGroups = [];

    /**
     * @return array
     */
    public static function getGroupsConditions(): array
    {
        return static::$groupsConditions;
    }

    public static function getGroupConditions(string $groupName): ?array
    {
        return static::$groupsConditions[$groupName] ?? null;
    }

    /**
     * Return conditions by conditional fields.
     */
    public static function getConditionalFields(): array
    {
        $conditionalFields = [];

        foreach (static::$groupsConditions as $constraint => $conditions) {
            $splittedConstraint = explode('.', $constraint);
            $targetFieldName = $splittedConstraint[0];

            foreach ($conditions as $fieldExpr => $condition) {
                $field = str_replace([' in', ' notin'], '', $fieldExpr);

                foreach ($conditions as $key => $cond) {
                    $fieldCond = str_replace([' in', ' notin'], '', $key);
                    if ($fieldCond === $field) {
                        $conditionalFields[$field][$targetFieldName][$constraint][] = [$key => $cond];
                    }
                }
            }
        }

        return $conditionalFields;
    }

    protected function getValidationGroups($pEntity): array
    {
        $this->validationGroups = ['Default'];
        foreach (static::$groupsConditions as $group => $conditions) {
            foreach ($conditions as $condition => $values) {
                $expr = explode(' ', $condition);
                $constraint = $expr[1];
                $properties = explode('.', $expr[0]);
                $value = null;

                foreach ($properties as $property) {
                    $getter = 'get'.ucfirst($property);
                    $entity = $value ?? $pEntity;

                    //boolean method
                    if (!method_exists($entity, $getter)) {
                        $getter = 'is'.ucfirst($property);
                    }

                    if (method_exists($entity, $getter)) {
                        $value = $entity->$getter();
                    }
                }

                if ('in' === $constraint && in_array($value, $values, true)) {
                    $this->validationGroups[] = $group;
                    break;
                } elseif ('notin' === $constraint && !in_array($value, $values, true)) {
                    $this->validationGroups[] = $group;
                    break;
                }
            }
        }

        return $this->validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection' => false,
            'extra_fields_message' => ApiProblem::FORM_EXTRA_FIELDS_ERROR,
            'data_class' => static::$dataClass,
            'validation_groups' => function (FormInterface $form) {
                return $this->getValidationGroups($form->getData());
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return static::getDataClassShortName();
    }

    /**
     * @return string
     */
    protected static function getDataClassShortName()
    {
        return lcfirst(substr(static::$dataClass, strrpos(static::$dataClass, '\\') + 1));
    }
}
