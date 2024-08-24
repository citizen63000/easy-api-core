<?php

namespace EasyApiCore\Model;

class EntityConfiguration
{
    public const inheritanceTypeColumnName = 'discriminator_type';

    protected ?string $entityType = null;

    protected ?string $schema = null;

    protected ?string $tableName = null;

    protected bool $mappedSuperclass = false;

    protected ?string $entityName = null;

    protected ?string $entityFileClassPath = null;

    protected ?string $namespace = null;

    protected ?string $properties = null;

    protected ?EntityConfiguration $parentEntity = null;

    protected bool $isParentEntity = false;

    protected ?string $repositoryClass = null;

    protected ?string $inheritanceType = null;

    protected bool $isTimestampable = false;

    protected array $fields = [];

    public function __toString()
    {
        return (string) $this->getEntityName();
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): void
    {
        $this->entityType = $entityType;
    }

    public function addField(EntityField $field): void
    {
        $field->setEntity($this);
        $this->fields[] = $field;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }

    public function setSchema(?string $schema): void
    {
        $this->schema = $schema;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function setTableName(?string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function isMappedSuperclass(): bool
    {
        return $this->mappedSuperclass;
    }

    public function setMappedSuperclass(bool $mappedSuperclass): EntityConfiguration
    {
        $this->mappedSuperclass = $mappedSuperclass;
        return $this;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function getEntityFileClassPath(): string
    {
        return $this->entityFileClassPath;
    }

    public function getEntityFileClassName(): ?string
    {
        if($name = $this->getEntityName()) {

            return "{$name}.php";
        }

        return null;
    }

    public function getEntityFileClassDirectory(): string
    {
        return str_replace($this->getEntityFileClassName(), '', $this->getEntityFileClassPath());
    }

    public function setEntityFileClassPath(string $entityFileClassPath): void
    {
        $this->entityFileClassPath = $entityFileClassPath;
    }

    /**
     * @return EntityField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function isTimestampable(): ?bool
    {
        return $this->isTimestampable;
    }

    public function setIsTimestampable(bool $isTimestampable = null): void
    {
        $this->isTimestampable = $isTimestampable;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getParentEntity(): ?EntityConfiguration
    {
        return $this->parentEntity;
    }

    public function setParentEntity(EntityConfiguration $parentEntity = null): void
    {
        $this->parentEntity = $parentEntity;
    }

    public function isParentEntity(): bool
    {
        return $this->isParentEntity;
    }

    public function setIsParentEntity(bool $isParentEntity): void
    {
        $this->isParentEntity = $isParentEntity;
    }

    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    public function setRepositoryClass(string $repositoryClass): void
    {
        $this->repositoryClass = $repositoryClass;
    }

    /**
     * Return the name of the bundle.
     */
    public function getBundleName()
    {
        $tab = explode('\\', $this->getNamespace());

        return $tab[0];
    }

    /**
     * Return the name of the context if a context is used.
     */
    public function getContextName(): ?string
    {
        $tab = explode('\\', $this->getNamespace());
        unset($tab[0], $tab[1]);
        $context = implode('\\', $tab);

        return '' !== $context ? $context : null;
    }

    public function getContextNameForPath(): array|string|null
    {
        return str_replace('\\', '/', $this->getContextName());
    }

    /**
     * Return only idFields.
     */
    public function getIdFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isPrimary()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getNativeFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isNativeType()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getNativeFieldsNames(bool $withId = true): array
    {
        $fieldsNames = [];

        foreach ($this->getNativeFields() as $field) {
            if($withId || 'id' !== $field->getName()) {
                $fieldsNames[] = $field->getName();
            }
        }

        return $fieldsNames;
    }

    public function getEntityFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if (!$field->isNativeType()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getRequiredFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isRequired()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getInheritanceType(): ?string
    {
        return $this->inheritanceType;
    }

    public function setInheritanceType(string $inheritanceType): void
    {
        $this->inheritanceType = $inheritanceType;
    }

    public function isReferential(): bool
    {
        return 1 === preg_match('/^Ref[A-Z][a-z]+/', $this->getEntityName());
    }

    public function getFullName(): string
    {
        return "{$this->getNamespace()}\\{$this->getEntityName()}";
    }

    public function hasUuid(): bool
    {
        foreach ($this->fields as $field) {
            if('uuid' === $field->getType()) {
                return true;
            }
        }

        return false;
    }

    public function hasField(string $fieldName, string $fieldType = null, bool $isPrimary = null): bool
    {
        foreach ($this->getFields() as $field) {

            $isFound = $fieldName === $field->getName();
            $isFound =  $isFound && null !== $fieldType ? ($fieldType === $field->getType()) : $isFound;
            $isFound =  $isFound && null !== $isPrimary ? ($isPrimary === $field->isPrimary()) : $isFound;

            if($isFound) {
                return true;
            }
        }

        return false;
    }

    public function getField(string $fieldName): ?EntityField
    {
        foreach ($this->getFields() as $field) {
            if($fieldName === $field->getName()) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function removeField(string $fieldName): static
    {
        foreach ($this->getFields() as $key => $field) {
            if($fieldName === $field->getName()) {
                unset($this->fields[$key]);
                return $this;
            }
        }
        throw new \Exception("Unknow fieldname {$fieldName} to remove in configuration");
    }

    public static function getEntityNameFromNamespace(string $namespace): string
    {
        $tab = explode('\\', $namespace);

        return $tab[count($tab)-1];
    }
}
