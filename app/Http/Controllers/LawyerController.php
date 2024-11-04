<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialityLawyerRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\User;
use App\Lawyer;
use App\Helpers;
use App\ReplyCategory;
use App\LawyerSpeciality;
use Illuminate\Http\Request;
use App\Http\Requests\CreateLawyerRequest;

class LawyerController extends Controller
{
    protected $data;

    public function index(Lawyer $lawyer, Request $request): View
    {
        $this->data['lawyers'] = $lawyer;
        $order_by              = 'DESC';
        if ($request->orderby == '') {
            $order_by = 'ASC';
        }

        //TODO refactor search functionality...
        //use some searchable package..

        if ($request->has('term') && $request->get('term')) {
            $term                  = $request->get('term');
            $this->data['term']    = $term;
            $this->data['lawyers'] = $this->data['lawyers']->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('email', 'like', '%' . $term . '%')
                    ->orWhereHas('lawyerSpeciality', function ($speciality) use ($term) {
                        $speciality->where('title', 'like', '%' . $term . '%');
                    });
            });
        }

        if ($request->sortby == 'speciality') {
            $this->data['lawyers'] = $this->data['lawyers']->join(
                'lawyer_specialities',
                'lawyer_specialities.id', '=', 'lawyers.speciality_id'
            )->orderBy('lawyer_specialities.title', $order_by);
        }
        $this->data['orderby']      = $order_by;
        $this->data['specialities'] = LawyerSpeciality::pluck('title', 'id');
        if ($request->has('with_archived') && $request->get('with_archived') == 'on') {
            $this->data['lawyers'] = Lawyer::onlyTrashed();
        }
        $this->data['lawyers'] = $this->data['lawyers']->with(['chat_message' => function ($chat_message) {
            $chat_message->select('id', 'message', 'lawyer_id', 'status')->orderByDesc('id');
        }, 'lawyerSpeciality']);
        $this->data['lawyers'] = $this->data['lawyers']->paginate(50);

        return view('lawyer.index', $this->data);
    }

    public function store(CreateLawyerRequest $request): RedirectResponse
    {
        $lawyer                = new Lawyer($request->all());
        $lawyer->default_phone = $request->get('phone');
        $lawyer->save();

        return redirect()->route('lawyer.index')->withSuccess('You have successfully saved a lawyer!');
    }

    public function update(CreateLawyerRequest $request, Lawyer $lawyer): RedirectResponse
    {
        $lawyer->fill($request->all())->save();

        return redirect()->route('lawyer.index')->withSuccess('You have successfully saved a lawyer!');
    }

    public function show(Lawyer $lawyer): View
    {
        $this->data['lawyer']           = $lawyer;
        $this->data['specialities']     = LawyerSpeciality::all();
        $this->data['reply_categories'] = ReplyCategory::all();
        $this->data['users_array']      = Helpers::getUserArray(User::all());

        return view('lawyer.show', $this->data);
    }

    public function destroy(Lawyer $lawyer): RedirectResponse
    {
        $lawyer->delete();

        return redirect()->route('lawyer.index')->withSuccess('You have successfully deleted a lawyer');
    }

    public function storeSpeciality(StoreSpecialityLawyerRequest $request): RedirectResponse
    {
        LawyerSpeciality::create(['title' => $request->get('title')]);

        return redirect()->route('lawyer.index')->withSuccess('You have successfully created a lawyer speciality!');
    }
}
