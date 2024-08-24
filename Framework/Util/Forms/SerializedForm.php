<?php

namespace EasyApiCore\Util\Forms;

use Symfony\Component\Serializer\Annotation\Groups;

class SerializedForm
{
    public const PARENT_TYPE_FORM = 'FORM';
    public const PARENT_TYPE_COLLECTION = 'COLLECTION';

    /**
     * @Groups({"public"})
     */
    protected string $name;

    /**
     * @Groups({"public"})
     */
    protected string $route;

    /**
     * @var SerializedFormField[]
     * @Groups({"public"})
     */
    protected array $fields;

    /**
     * @Groups({"private"})
     */
    private ?string $parentType = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): void
    {
        $this->route = $route;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(SerializedFormField $field): void
    {
        $field->setParentForm($this);
        $this->fields[] = $field;
    }

    public function removeField(string $fieldName): SerializedForm
    {
        foreach ($this->fields as $key => $field) {
            if ($fieldName === $field->getName()) {
                unset($this->fields[$key]);
                sort($this->fields);

                break;
            }
        }

        return $this;
    }

    public function getField(string $name): ?SerializedFormField
    {
        foreach ($this->fields as $field) {
            if ($name === $field->getName()) {
                return $field;
            }
        }

        return null;
    }

    public function getParentType(): ?string
    {
        return $this->parentType;
    }

    public function setParentType(string $parentType = null): void
    {
        $this->parentType = $parentType;
    }

    public function isInCollection(): bool
    {
        return $this->getParentType() === self::PARENT_TYPE_COLLECTION;
    }
}
