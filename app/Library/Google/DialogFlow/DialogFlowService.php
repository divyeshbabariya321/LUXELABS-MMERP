<?php

namespace App\Library\Google\DialogFlow;

use App\ChatbotQuestion;
use App\Models\DialogflowEntityType;
use Google\Cloud\Dialogflow\V2\AgentsClient;
use Google\Cloud\Dialogflow\V2\EntityType;
use Google\Cloud\Dialogflow\V2\EntityType\Entity;
use Google\Cloud\Dialogflow\V2\EntityType\Kind;
use Google\Cloud\Dialogflow\V2\EntityTypesClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;

class DialogFlowService
{
    const VARIABLES = [
        'order_id',
        'order_status',
        'website',
        'estimate_date',
        'delivery_date',
        'awb_number',
        'refund_status',
        'refund_id',
    ];

    private $credentials;

    public function __construct(private $googleAccount)
    {
        $this->credentials = ['credentials' => json_decode(file_get_contents($googleAccount->service_file), true)];
    }

    public function createIntent($parameters, $updateId = null)
    {
        if (! $updateId) {
            $intents = $this->listIntents();
            $findIntent = array_search($parameters['name'], array_column($intents, 'display_name'));
            if ($findIntent) {
                $updateId = $intents[$findIntent]['id'];
            }
        }
        // Create Intents
        $intentClient = new IntentsClient($this->credentials);
        if ($updateId) {
            $parent = $intentClient->intentName($this->googleAccount->project_id, $updateId);
        } else {
            $parent = $intentClient->agentName($this->googleAccount->project_id);
        }

        // Training Phrase
        $trainingPhrases = [];
        foreach ($parameters['questions'] as $question) {
            $part = (new Part)->setText($question);
            $trainingPhrase = (new TrainingPhrase)->setParts([$part]);
            $trainingPhrases[] = $trainingPhrase;
        }

        // Prepare message for intent
        $messages = [];
        foreach ($parameters['reply'] as $reply) {
            $text = (new Text)->setText([$reply]);
            $messages[] = (new Message)->setText($text);
        }

        // Prepare Intent
        $intent = (new Intent)->setDisplayName($parameters['name'])
            ->setTrainingPhrases($trainingPhrases)
            ->setMessages($messages);

        if (isset($parameters['parent']) && ! empty($parameters['parent'])) {
            $parentQuestion = ChatbotQuestion::find($parameters['parent']);
            if ($parentQuestion && $parentQuestion->google_response_id) {
                $parentClient = new IntentsClient($this->credentials);
                $parentFollowupName = $parentClient->intentName($this->googleAccount->project_id, $parentQuestion->google_response_id);
                $intent->setParentFollowupIntentName($parentFollowupName);
            }
        }

        if ($updateId) {
            $intent->setName($parent);
            $response = $intentClient->updateIntent($intent);
        } else {
            $response = $intentClient->createIntent($parent, $intent);
        }
        $intentClient->close();

        return $response->getName();
    }

    public function deleteIntent($parameters)
    {
        $intentsClient = new IntentsClient($this->credentials);
        $projectId = $this->googleAccount->project_id;
        $intentId = $parameters;
        $intentName = $intentsClient->intentName($projectId, $intentId);
        $response = $intentsClient->deleteIntent($intentName);
        $intentsClient->close();

        return $response;
    }

    public function listIntents()
    {
        $intentsClient = new IntentsClient($this->credentials);
        $parent = $intentsClient->agentName($this->googleAccount->project_id);
        $response = $intentsClient->listIntents($parent, ['pageSize' => 1000]);

        $intents = [];
        foreach ($response->iterateAllElements() as $intent) {
            $name = explode('/', $intent->getName());
            $name = $name[count($name) - 1];
            $intents[] = ['display_name' => $intent->getDisplayName(), 'id' => $name];
        }

        return $intents;
    }

    public function createEntity($entityTypeId, $entityValue, $synonyms = [])
    {
        // synonyms must be exactly [$entityValue] if the entityTypes'
        // kind is KIND_LIST
        if (! $synonyms) {
            $synonyms = [$entityValue];
        }

        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($this->googleAccount->project_id, $entityTypeId);

        // prepare entity
        $entity = new Entity;
        $entity->setValue($entityValue);
        $entity->setSynonyms($synonyms);

        // create entity
        $response = $entityTypesClient->batchCreateEntities($parent, [$entity]);
        $entityTypesClient->close();

        return $response->getName();
    }

    public function deleteEntity($entityTypeId, $entityValue)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($this->googleAccount->project_id, $entityTypeId);
        $response = $entityTypesClient->batchDeleteEntities($parent, [$entityValue]);
        $entityTypesClient->close();

        return $response;
    }

    public function createEntityType($displayName, $kind = Kind::KIND_MAP)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);

        // prepare entity type
        $parent = $entityTypesClient->agentName($this->googleAccount->project_id);
        $entityType = new EntityType;
        $entityType->setDisplayName(preg_replace('/\s+/', '_', $displayName));
        $entityType->setKind($kind);

        // create entity type
        $response = $entityTypesClient->createEntityType($parent, $entityType);
        $entityTypesClient->close();

        return $response->getName();
    }

    public function deleteEntityType($entityTypeId)
    {
        $entityTypesClient = new EntityTypesClient($this->credentials);
        $parent = $entityTypesClient->entityTypeName($this->googleAccount->project_id, $entityTypeId);
        $response = $entityTypesClient->deleteEntityType($parent);
        $entityTypesClient->close();

        return $response;
    }

    public function detectIntent($sessionId, $text)
    {
        $sessionsClient = new SessionsClient($this->credentials);
        $session = $sessionsClient->sessionName($this->googleAccount->project_id, $sessionId ?: uniqid());

        $textInput = new TextInput;
        $textInput->setText($text);
        $textInput->setLanguageCode('en-US');

        // create query input
        $queryInput = new QueryInput;
        $queryInput->setText($textInput);
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $sessionsClient->close();

        return $response->getQueryResult();
    }

    public function trainAgent()
    {
        $agentsClient = new AgentsClient($this->credentials);
        $parent = $agentsClient->projectName($this->googleAccount->project_id);
        $response = $agentsClient->trainAgent($parent);
        $agentsClient->close();

        return $response;
    }

    public function deleteQuestion($question)
    {
        if ($question->keyword_or_question === 'intent' && $question->google_response_id) {
            $this->deleteIntent($question->google_response_id);
        } elseif ($question->keyword_or_question === 'entity') {
            $ids = [];
            foreach ($question->chatbotQuestionExamples as $qu) {
                $ids[] = $qu->types;
            }
            if ($ids) {
                $entityType = DialogflowEntityType::whereIn('id', $ids)->first();
                $this->deleteEntity($entityType->response_id, $question->value);
            }
        }
    }

    public function findVariables($text): array
    {
        $variables = [];
        foreach (self::VARIABLES as $VARIABLE) {
            if (str_contains($text, '#{'.$VARIABLE.'}')) {
                $variables[] = $VARIABLE;
            }
        }

        return $variables;
    }

    public function purifyResponse($text, $customer = null, $orderId = null, $refundId = null)
    {
        $variables = $this->findVariables($text);
        if (count($variables) > 0) {
            foreach ($variables as $variable) {
                $text = $this->getReplacement($text, $variable, $customer, $orderId, $refundId);
            }
        }

        return $text;
    }

    public function getReplacement($text, $variable, $customer, $orderId = null, $refundId = null)
    {
        $lastOrder = null;
        $latestRefund = null;
        if ($customer) {
            if ($orderId) {
                $lastOrder = $customer->getOrderById($orderId);
            } else {
                $lastOrder = $customer->latestOrder();
            }
            if ($refundId) {
                $latestRefund = $customer->getRefundById($refundId);
            } else {
                $latestRefund = $customer->latestRefund();
            }
        }
        switch ($variable) {
            case 'order_id':
                if ($lastOrder) {
                    $text = str_replace('#{order_id}', $lastOrder->order_id, $text);
                }
                break;
            case 'order_status':
                if ($lastOrder) {
                    $text = str_replace('#{order_status}', $lastOrder->status->status, $text);
                }
                break;
            case 'website':
                if ($lastOrder) {
                    $text = str_replace('#{website}', $lastOrder->getWebsiteTitle(), $text);
                }
                break;
            case 'estimate_date':
                if ($lastOrder) {
                    $text = str_replace('#{estimate_date}', $lastOrder->estimated_delivery_date, $text);
                }
                break;
            case 'delivery_date':
                if ($lastOrder) {
                    $text = str_replace('#{delivery_date}', $lastOrder->date_of_delivery, $text);
                }
                break;
            case 'awb_number':
                if ($lastOrder) {
                    $text = str_replace('#{awb_number}', $lastOrder->totalWayBills(), $text);
                }
                break;
            case 'refund_status':
                if ($latestRefund && $latestRefund->returnExchangeStatus) {
                    $text = str_replace('#{refund_status}', $latestRefund->returnExchangeStatus->status_name, $text);
                }
                break;
            case 'refund_id':
                if ($latestRefund && $latestRefund->returnExchangeStatus) {
                    $text = str_replace('#{refund_id}', $latestRefund->id, $text);
                }
                break;
            default:
                break;
        }

        return $text;
    }
}
