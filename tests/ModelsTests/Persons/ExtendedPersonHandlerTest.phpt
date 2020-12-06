<?php

namespace FKSDB\Tests\ModelTests\Person;

$container = require '../../Bootstrap.php';

use FKSDB\Components\Forms\Containers\SearchContainer\PersonSearchContainer;
use FKSDB\Modules\Core\BasePresenter;
use FKSDB\Components\Forms\Containers\Models\ContainerWithOptions;
use FKSDB\Components\Forms\Factories\ReferencedPerson\ReferencedPersonFactory;
use FKSDB\ORM\IModel;
use FKSDB\ORM\Models\ModelContest;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\ORM\Services\ServiceContest;
use FKSDB\ORM\Services\ServiceContestant;
use FKSDB\Tests\MockEnvironment\MockApplicationTrait;
use FKSDB\Tests\ModelTests\DatabaseTestCase;
use Nette\DI\Container;
use Nette\Forms\Form;
use FKSDB\Persons\ExtendedPersonHandler;
use FKSDB\Persons\ExtendedPersonHandlerFactory;
use FKSDB\Persons\IExtendedPersonPresenter;
use FKSDB\Persons\IModifiabilityResolver;
use FKSDB\Persons\IVisibilityResolver;
use FKSDB\Persons\ReferencedPersonHandler;
use Tester\Assert;

class ExtendedPersonHandlerTest extends DatabaseTestCase {
    use MockApplicationTrait;

    private ExtendedPersonHandler $fixture;

    private ReferencedPersonFactory $referencedPersonFactory;

    /**
     * ExtendedPersonHandlerTest constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->setContainer($container);
        $this->referencedPersonFactory = $this->container->getByType(ReferencedPersonFactory::class);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->mockApplication();
        $handlerFactory = $this->getContainer()->getByType(ExtendedPersonHandlerFactory::class);

        $service = $this->getContainer()->getByType(ServiceContestant::class);
        $contest = $this->container->getByType(ServiceContest::class)->findByPrimary(ModelContest::ID_FYKOS);
        $this->fixture = $handlerFactory->create($service, $contest, 1, 'cs');
    }

    protected function tearDown(): void {
        $this->connection->query('DELETE FROM contestant_base');
        $this->connection->query('DELETE FROM auth_token');
        $this->connection->query('DELETE FROM login');

        parent::tearDown();
    }

    public function testNewPerson(): void {

        $presenter = new PersonPresenter();
        // Define a form

        $form = $this->createForm([
            'person' => [
                'other_name' => [
                    'required' => true,
                ],
                'family_name' => [
                    'required' => true,
                ],
            ],
            'person_history' => [
                'school_id' => [
                    'required' => true,
                ],
                'study_year' => [
                    'required' => true,
                ],
                'class' => [
                    'required' => false,
                ],
            ],
            'post_contact_p' => [
                'address' => [
                    'required' => true,
                ],
            ],
            'person_info' => [
                'email' => [
                    'required' => true,
                ],
                'origin' => [
                    'required' => false,
                ],
                'agreed' => [
                    'required' => true,
                ],
            ],
        ], 2000);

        // Fill user data
        $form->setValues([
            ExtendedPersonHandler::CONT_AGGR => [
                ExtendedPersonHandler::EL_PERSON => "__promise",
                ExtendedPersonHandler::EL_PERSON . '_1' => [
                    '_c_compact' => " ",
                    'person' => [
                        'other_name' => "Jana",
                        'family_name' => "Triková",
                    ],
                    'person_history' => [
                        'school_id__meta' => "JS",
                        'school_id' => "1",
                        'study_year' => "2",
                        'class' => "2.F",
                    ],
                    'post_contact_p' => [
                        'address' => [
                            'target' => "Krtkova 12",
                            'city' => "Pohádky",
                            'postal_code' => "43243",
                            'country_iso' => null,
                        ],
                    ],
                    'person_info' => [
                        'email' => "jana@sfsd.com",
                        'origin' => "dfsd",
                        'agreed' => "on",
                    ],
                ],
            ],
        ]);
        $form->validate();

        // Check
        $result = $this->fixture->handleForm($form, $presenter, true);
        Assert::same(ExtendedPersonHandler::RESULT_OK_NEW_LOGIN, $result);

        $person = $this->fixture->getPerson();
        Assert::same('Jana', $person->other_name);
        Assert::same('Triková', $person->family_name);

        $contestants = $person->getContestants(ModelContest::ID_FYKOS);
        Assert::same(1, count($contestants));

        $info = $person->getInfo();
        Assert::same('jana@sfsd.com', $info->email);

        $address = $person->getPermanentAddress();
        Assert::same('Krtkova 12', $address->target);
        Assert::same('43243', $address->postal_code);
        Assert::notEqual(null, $address->region_id);
    }

    private function createForm(array $fieldsDefinition, int $acYear): Form {
        $form = new Form();
        $container = new ContainerWithOptions();
        $form->addComponent($container, ExtendedPersonHandler::CONT_AGGR);

        $referencedId = $this->referencedPersonFactory->createReferencedPerson(
            $fieldsDefinition,
            $acYear,
            PersonSearchContainer::SEARCH_NONE,
            false,
            new TestResolver(),
            new TestResolver()
        );

        $container->addComponent($referencedId, ExtendedPersonHandler::EL_PERSON);
        // $container->addComponent($component->getReferencedContainer(), ExtendedPersonHandler::CONT_PERSON);

        return $form;
    }
}

/*
 * Mock classes
 */

class PersonPresenter extends BasePresenter implements IExtendedPersonPresenter {

    public function getModel(): ?IModel {
        return null;
    }

    public function messageCreate(): string {
        return '';
    }

    public function messageEdit(): string {
        return '';
    }

    public function messageError(): string {
        return '';
    }

    public function messageExists(): string {
        return '';
    }

    public function flashMessage($message, string $type = 'info'): \stdClass {
        return new \stdClass();
    }

}

class TestResolver implements IVisibilityResolver, IModifiabilityResolver {

    public function getResolutionMode(ModelPerson $person): string {
        return ReferencedPersonHandler::RESOLUTION_EXCEPTION;
    }

    public function isModifiable(ModelPerson $person): bool {
        return true;
    }

    public function isVisible(ModelPerson $person): bool {
        return true;
    }

}

$testCase = new ExtendedPersonHandlerTest($container);
$testCase->run();
