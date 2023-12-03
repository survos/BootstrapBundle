<?php

namespace Survos\BootstrapBundle\Translation;

use Survos\Providence\Services\ProfileService;
use Survos\Providence\XmlModel\Profile;
use Survos\Providence\XmlModel\ProfileLabel;
use Survos\Providence\XmlModel\ProfileList;
use Survos\Providence\XmlModel\ProfileLists;
use Survos\Providence\XmlModel\ProfileMetaDataElement;
use Survos\Providence\XmlModel\ProfileRelationshipTableType;
use Survos\Providence\XmlModel\ProfileScreen;
use Survos\Providence\XmlModel\ProfileUserInterface;
use Survos\Providence\XmlModel\ProfileUserInterfaces;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use function Symfony\Component\String\u;

class RoutesTranslationLoader implements LoaderInterface
{
    public function __construct(
        #[AutowireIterator(tag: 'controller.service_arguments')] private $taggedServices,
    ) {
//        assert(false, "hello from " . __CLASS__);
    }

    /**
     * Loads a locale.
     *
     * @param mixed  $resource A resource
     * @param string $domain   The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($resource, $symfonyLocale, $domain = 'messages'): MessageCatalogue
    {
        $localMap = [];
        $catalogue = null;
        $translations = [];

        $taggedServices = $this->taggedServices;
//        $taggedServices = $container->findTaggedServiceIds('container.service_subscriber');
//             $taggedServices = $container->findTaggedServiceIds('controller.service_arguments');
        foreach ($taggedServices as $controllerClass) {
//            if (!class_exists($controllerClass)) {
//                continue;
//            }
            $reflectionClass = new \ReflectionClass($controllerClass);
            foreach ($reflectionClass->getMethods() as $method) {
                foreach ($method->getAttributes(Route::class) as $attribute) {
                    $args = $attribute->getArguments();
                    $methodName = $method->getName();
                    $name = $args['name'] ?? $methodName;
                    $translations[$name] = u($methodName)->snake()->replace('_', ' ')->title()->toString();
                }
            }
        }
//        dd($translations);
        $catalogue = new MessageCatalogue(
            $symfonyLocale,
            [
                $domain => $translations,
            ]
        );
        return $catalogue;


        return new MessageCatalogue($symfonyLocale);

        $localMap = [
            'en' => 'en_US',
            'fr' => 'fr_FR',
        ];
        $locale = $localMap[$symfonyLocale];
        // this is for testing only, move to a database for deployment
        $profiles = $this->profileService->loadXml();
        $translations = [];
        /** @var \App\Entity\Profile $p */
        foreach ($profiles as $p) {
            $profile = $p->getXmlProfile();
            /** @var ProfileUserInterface $ui */
            foreach ($profile->getUserInterfaces() as $ui) {
                $labels = array_filter($ui->getLabels(), fn (ProfileLabel $label) => $label->locale == 'en_US');
                if ($l = array_pop($labels)) {
                    $translations[$ui->_label()] = $l->name;
                    $translations[$ui->_description()] = $l->description ?: " ";
                }

                /** @var ProfileScreen $screen */
                foreach ($ui->getScreens() as $screen) {
                    $labels = array_filter($screen->getLabels(), fn (ProfileLabel $label) => $label->locale == 'en_US');

                    if ($l = array_pop($labels)) {
                        $translations[$screen->_label()] = $l->name;
                        $translations[$screen->_description()] = $l->description ?: " ";
                    }
                    //                    dd($l, $translations);
                }
            }
            //            dd($translations);

            /** @var ProfileLists $list */
            foreach ($profile->getLists() as $list) {
                $labels = array_filter($list->getLabels(), fn (ProfileLabel $label) => $label->locale == $locale);
                if ($l = array_pop($labels)) {
                    $translations[$list->_label()] = html_entity_decode((string) $l->name);
                    $translations[$list->_description()] = html_entity_decode((string) $l->description) ?: " ";
                }
                foreach ($list->getItems() as $item) {
                    $labels = array_filter($item->getLabels(), fn (ProfileLabel $label) => $label->locale == $locale);
                    if ($l = array_pop($labels)) {
                        $translations[$item->_t($list)] = $l->name ?: $l->name_singular;
                        //                        $translations[$item->_label()] = $l->name;
                        assert(empty($l->description), "Item has description");
                        //                        $translations[$item->_description()] = $l->description;
                    }
                }
            }

            foreach ($profile->getRelationshipTables() as $table) {
                /** @var ProfileRelationshipTableType $element */
                foreach ($table->getTypes() as $element) {
                    $labels = array_filter($element->getLabels(), fn (ProfileLabel $label) => $label->locale == $locale);
                    if ($l = array_pop($labels)) {
                        $translations[$element->_label()] = $l->name;
                        $translations[$element->_description()] = $l->description;
                        $translations[$element->_typename()] = $l->typename;
                        $translations[$element->_typename_reverse()] = $l->typename_reverse;
                    }
                }
            }

            foreach (['getElements'] as $method) {
                /** @var ProfileMetaDataElement $element */
                foreach ($profile->{$method}() as $element) {
                    $labels = array_filter($element->getLabels(), fn (ProfileLabel $label) => $label->locale == $locale);
                    if ($l = array_pop($labels)) {
                        $translations[$element->_label()] = $l->name;
                        $translations[$element->_description()] = $l->description ?: ' ';
                    }

                    // it might be nested
                    if (! empty($element->elements)) {
                        foreach ($element->getElements() as $nestedElement) {
                            $labels = array_filter($nestedElement->getLabels(), fn (ProfileLabel $label) => $label->locale == $locale);
                            if ($l = array_pop($labels)) {
                                $translations[$element->_label()] = $l->name;
                                $translations[$element->_description()] = $l->description ?: ' ';
                            }
                        }
                    }

                    //                    {% for e in mde.elements %}
                    //                    <li>{{ e.value.code }} <i>{{ e.value.datatype }} {{ e.value._label|trans }}</i></li>
                    //{{ dump(e.value) }}
                    //                        {% endfor %}
                }
            }
            // maybe use xpath to get all the Labels?
        }
        //        $messages = $this->getRepository()->findByDomainAndLocale($domain, $locale);
        //        $values = array_map(static function (EntityInterface $entity) {
        //            return $entity->getTranslation();
        //        }, $messages);
        $catalogue = new MessageCatalogue(
            $symfonyLocale,
            [
                $domain => $translations,
            ]
        );
        return $catalogue;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): TranslationRepositoryInterface
    {
        return $this->doctrine->getRepository($this->entityClass);
    }
}
