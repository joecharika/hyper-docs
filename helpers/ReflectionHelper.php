<?php


namespace Helpers;


use Hyper\Functions\Str;
use Hyper\Reflection\Annotation;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

abstract class ReflectionHelper
{
    #region Public
    public static function getClass(ReflectionClass $ref): object
    {
        $optimisedDoc = self::getOptimisedDoc($ref->getDocComment());
        $name = $ref->getName();

        $prop = function ($prop) {
            return self::getProperty($prop);
        };
        $filPrivate = function ($prop) {
            return $prop->access !== 'private';
        };
        $func = function ($prop) use ($name) {
            $prop = new ReflectionMethod($name, $prop);
            return self::getMethod($prop);
        };

        return (object)[
            'name' => $name,
            'description' => $optimisedDoc,
            'deprecated' => Str::contains($optimisedDoc, '@deprecated'),
            'properties' => array_filter(array_map($prop, $ref->getProperties()), $filPrivate),
            'constants' => array_map(function ($const) use ($name) {
                return self::getConstant($const);
            }, $ref->getReflectionConstants()),
            'methods' => array_map($func, get_class_methods($name))
        ];
    }

    private static function getOptimisedDoc(string $doc): string
    {
        $doc = str_replace("/*", '', $doc);
        $doc = str_replace("*/", '', $doc);
        $doc = str_replace("*", '', $doc);
        $doc = preg_replace(
            "/@(.*?)[\n]/s",
            '',
            $doc
        );
        return Str::trimLine($doc, '<br>') . '<br>';
    }

    public static function getProperty(ReflectionProperty $ref): object
    {
        $optimisedDoc = self::getOptimisedDoc($ref->getDocComment());
        $name = $ref->getName();
        return (object)[
            'name' => $name,
            'description' => $optimisedDoc,
            'deprecated' => Str::contains($optimisedDoc, '@deprecated'),
            'access' => self::getAccessModifier($ref)
        ];
    }

    private static function getAccessModifier(Reflector $ref): string
    {
        $access = 'default';
        $access = $ref->isPrivate() ? 'private' : $access;
        $access = $ref->isProtected() ? 'protected' : $access;
        $access = $ref->isPublic() ? 'public' : $access;
        return $ref->isStatic() ? $access . ' static' : $access;
    }
    #endregion

    #region Private

    public static function getMethod(ReflectionMethod $ref): object
    {
        $optimisedDoc = self::getOptimisedDoc($ref->getDocComment());
        $name = $ref->getName();
        return (object)[
            'name' => $name,
            'description' => $optimisedDoc,
            'deprecated' => Str::contains($optimisedDoc, '@deprecated'),
            'access' => self::getAccessModifier($ref),
            'return' => str_replace('|', ' | ', $ref->getReturnType()
                ?? Annotation::getMethodAnnotation(
                    $ref->class,
                    $name,
                    'return')
                ?? 'void|mixed'),
            'params' => $ref->getParameters()
        ];
    }

    public static function getConstant(ReflectionClassConstant $ref): object
    {
        $optimisedDoc = self::getOptimisedDoc($ref->getDocComment());
        return (object)[
            'name' => $ref->getName(),
            'value' => $ref->getValue(),
            'description' => $optimisedDoc,
            'deprecated' => Str::contains($optimisedDoc, '@deprecated'),
        ];
    }
    #endregion
}