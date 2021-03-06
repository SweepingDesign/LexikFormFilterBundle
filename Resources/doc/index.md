Installation
============

Update your `deps` and `deps.lock` files:

    // deps
    ...
    [LexikFormFilterBundle]
        git=https://github.com/lexik/LexikFormFilterBundle.git
        target=/bundles/Lexik/Bundle/FormFilterBundle

    // deps.lock
    ...
    LexikFormFilterBundle <commit>

Register the namespaces with the autoloader:

    // app/autoload.php
     $loader->registerNamespaces(array(
        // ...
        'Lexik' => __DIR__.'/../vendor/bundles',
        // ...
    ));

Register the bundle with your kernel:

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
        // ...
    );


Usage
=====

Here an example of how to use the bundle.
Once the bundle is loaded in your app, add this in your `app/config.yml`

```yaml
# app/config.yml
twig:
    form:
        resources:
            - LexikFormFilterBundle:Form:form_div_layout.html.twig
```


Let's use the following entity:

```php
<?php
// MyEntity.php
namespace Project\Bundle\SuperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MyEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rank;
}
```

Create a type extended from AbstractType, add `name` and `rank` and use the filter_xxxx types.

```php
<?php
// MySuperFilterType.php
namespace Project\Bundle\SuperBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MySuperFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'filter_text');
        $builder->add('rank', 'filter_number');
    }

    public function getName()
    {
        return 'my_super_filter';
    }
}
```

Then in an action, create a form object from the MySuperFilterType. Let's say we filter when the form is submitted with a post method.

```php
<?php
// DefaultController.php
namespace Project\Bundle\SuperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Project\Bundle\SuperBundle\Filter\MySuperFilterType;

class DefaultController extends Controller
{
    public function testFilterAction()
    {
        $form = $this->get('form.factory')->create(new MySuperFilterType());

        if ($this->get('request')->getMethod() == 'POST') {
            // bind values from the request
            $form->bindRequest($this->get('request'));

            // initliaze a query builder
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ProjectSuperBundle:MyEntity')
                ->createQueryBuilder('e');

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);

            // now look at the DQL =)
            var_dump($filterBuilder->getDql());
        }

        return $this->render('ProjectSuperBundle:Default:testFilter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
```

Basic template

```
// testFilter.html.twig
<form method="post">
    {{ form_rest(form) }}
    <input type="submit" name="submit-filter" value="filter" />
</form>
```

Override default way to apply the filter
========================================

All filter types have an `apply_filter` option which is a closure.
If this option is define the `QueryBuilderUpdater` won't call the `applyFilter()` method from the type class but it will call the given closure.

The closure take 4 parameters:

* the Doctrine query builder
* the expression class
* the field name
* an array of values containing the field value and some other data

```php
<?php
// MySuperFilterType.php
namespace Project\Bundle\SuperBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;
use Doctrine\ORM\QueryBuilder;

class MySuperFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'filter_text', array(
            'apply_filter' => function (QueryBuilder $filterBuilder, Expr $expr, $field, array $values) {
            
                // add conditions you need :)
                
            },
        ));
    }

    public function getName()
    {
        return 'my_super_filter';
    }
}
```

Embed filter types
==================

You can also embed some filters inside another one. 
Let's say the entity we filter in `MySuperFilterType` is related to some options and an option has a 2 fields: label and color. 
We can filter entities by their option's label and color by creating and using a `OptionsFilterType` inside `MySuperFilterType`:

```php
<?php

namespace Project\Bundle\SuperBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MySuperFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'filter_text');
        $builder->add('rank', 'filter_number');
        $builder->add('options', new OptionsFilterType());
    }

    public function getName()
    {
        return 'my_super_filter';
    }
}
```

The `OptionsFilterType` class is a standard form type which have to implements `Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\FilterTypeSharedableInterface`.
This interface define a `addShared()` method used to add joins (or other stuff) needed to be able to apply condition on fields from the embbed type (OptionsFilterType here).

```php
<?php

namespace Project\Bundle\SuperBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\QueryBuilder;

use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\FilterTypeSharedableInterface;

/**
 * Embbed filter type.
 */
class OptionsFilterType extends AbstractType implements FilterTypeSharedableInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'filter_text');
        $builder->add('color', 'filter_text');
    }

    public function getName()
    {
        return 'options_filter';
    }

    /**
     * This method aim to add all joins you need
     */
    public function addShared(FilterBuilderExecuterInterface $qbe)
    {
        $closure = function(QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
            // add the join clause to the doctrine query builder
            // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
            $filterBuilder->leftJoin($alias . '.options', 'opt');
        }
    
        // then use the query builder executor to define the join, the join's alias and things to do on the doctrine query builder.
        $qbe->addOnce($qbe->getAlias().'.options', 'opt', $closure);
    }
}
```
