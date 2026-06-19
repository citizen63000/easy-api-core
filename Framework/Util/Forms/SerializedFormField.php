<?php

namespace EasyApiCore\Util\Forms;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class SerializedFormField
{
    /**
     * @Groups({"public"})
     */
    protected ?string $name = null;

    /**
     * @Groups({"public"})
     */
    protected ?string $label = null;

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $placeholder;

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $key;

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $type = '';

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $format;

    /**
     * @var bool
     *
     * @Groups({"public"})
     */
    protected $required;

    /**
     * @var string[]
     *
     * @Groups({"public"})
     */
    protected $conditions = [];

    /**
     * @var string[]
     *
     * @Groups({"private"})
     */
    protected $validationGroups = [];

    /**
     * @var string[]
     *
     * @Groups({"public"})
     */
    protected $conditionedFields = [];

    /**
     * EmbeddedForm.
     *
     * @var SerializedForm
     *
     * @Groups({"public"})
     */
    protected $form;

    /**
     * @var SerializedForm
     *
     * @Groups({"private"})
     */
    protected $parentForm;

    /**
     * @var string[]
     *
     * @Groups({"public"})
     */
    protected $values = [];

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $widget = 'input';

    /**
     * @var mixed|null
     *
     * @OA\Property(type="string")
     *
     * @Groups({"public"})
     */
    protected $defaultValue = '';

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $group = '';

    /**
     * @var string[]
     *
     * @Groups({"public"})
     */
    protected $attr = [];

    /**
     * @var string
     *
     * @Groups({"public"})
     */
    protected $discriminator = '';

    /**
     * @var string[]
     *
     * @Groups({"public"})
     */
    private $dynamicChoices = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required = false): void
    {
        $this->required = $required;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions): void
    {
        $this->conditions = $conditions;
    }

    public function getValidationGroups(): array
    {
        return $this->validationGroups;
    }

    public function setValidationGroups(array $validationGroups): void
    {
        $this->validationGroups = $validationGroups;
    }

    public function getConditionedFields(): array
    {
        return $this->conditionedFields;
    }

    public function setConditionedFields(array $conditionedFields): void
    {
        $this->conditionedFields = $conditionedFields;
    }

    public function getForm(): ?SerializedForm
    {
        return $this->form;
    }

    public function setForm(SerializedForm $form): void
    {
        $this->form = $form;
    }

    public function getParentForm(): ?SerializedForm
    {
        return $this->parentForm;
    }

    public function setParentForm(?SerializedForm $parentForm = null): void
    {
        $this->parentForm = $parentForm;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function getWidget(): ?string
    {
        return $this->widget;
    }

    public function setWidget(?string $widget): void
    {
        $this->widget = $widget;
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed|null $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function setGroup(?string $group): void
    {
        $this->group = $group;
    }

    public function getAttr(): array
    {
        return $this->attr;
    }

    public function setAttr(array $attr): void
    {
        $this->attr = $attr;
    }

    public function getDiscriminator(): ?string
    {
        return $this->discriminator;
    }

    public function setDiscriminator(?string $discriminator): void
    {
        $this->discriminator = $discriminator;
    }

    public function getDynamicChoices(): array
    {
        return $this->dynamicChoices;
    }

    public function setDynamicChoices(array $dynamicChoices): void
    {
        $this->dynamicChoices = $dynamicChoices;
    }

    public function isReferential(): bool
    {
        return 1 === preg_match('/ref[A-Z][a-z]+/', $this->getName());
    }
}
