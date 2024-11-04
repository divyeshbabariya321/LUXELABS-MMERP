<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

class OldIncoming extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="serial_no",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="amount",type="float")
     * @SWG\Property(property="commitment",type="string")
     * @SWG\Property(property="communication",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $primaryKey = 'serial_no';

    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'amount',
        'commitment', 'communication',
        'status',
    ];

    /**
     * Protected Date
     *
     * @var array
     *
     * @param mixed $request
     */
    /**
     * Saving categories
     *
     * @param string $request Request attributes
     */
    public function saveRecord(string $request): Response
    {
        if (! empty($request)) {
            $this->name          = filter_var($request['name'], FILTER_SANITIZE_STRING);
            $this->description   = filter_var($request['description'], FILTER_SANITIZE_STRING);
            $this->amount        = filter_var($request['amount'], FILTER_SANITIZE_STRING);
            $this->commitment    = filter_var($request['commitment'], FILTER_SANITIZE_STRING);
            $this->communication = filter_var($request['communication'], FILTER_SANITIZE_STRING);
            $this->status        = filter_var($request['status'], FILTER_SANITIZE_STRING);
            $this->email         = filter_var($request['email'], FILTER_SANITIZE_STRING);
            $this->number        = filter_var($request['number'], FILTER_SANITIZE_STRING);
            $this->address       = filter_var($request['address'], FILTER_SANITIZE_STRING);
            $this->save();

            return 'sucess';
        }
    }

    /**
     * Saving categories
     *
     * @param string $request   Request attributes
     * @param mixed  $serial_no
     */
    public function updateRecord(string $request, $serial_no): Response
    {
        if (! empty($request) || ! empty($serial_no)) {
            $incoming                = self::findOrFail($serial_no);
            $incoming->name          = filter_var($request['name'], FILTER_SANITIZE_STRING);
            $incoming->description   = filter_var($request['description'], FILTER_SANITIZE_STRING);
            $incoming->amount        = filter_var($request['amount'], FILTER_SANITIZE_STRING);
            $incoming->commitment    = filter_var($request['commitment'], FILTER_SANITIZE_STRING);
            $incoming->communication = filter_var($request['communication'], FILTER_SANITIZE_STRING);
            $incoming->status        = filter_var($request['status'], FILTER_SANITIZE_STRING);
            $incoming->email         = filter_var($request['email'], FILTER_SANITIZE_STRING);
            $incoming->number        = filter_var($request['number'], FILTER_SANITIZE_STRING);
            $incoming->address       = filter_var($request['address'], FILTER_SANITIZE_STRING);
            $incoming->save();

            return 'sucess';
        }
    }

    /**
     * Get Status
     */
    public static function getStatus(): Response
    {
        $types = [
            'pending'  => 'pending',
            'disputed' => 'disputed',
            'settled'  => 'settled',
            'paid'     => 'paid',
            'closed'   => 'closed',
        ];

        return $types;
    }
}
