<?php

namespace App\Http\Controllers;
use App\Http\Controllers;

use Illuminate\View\View;
use App\Models\MindMapDiagram;
use App\Http\Requests\StoreMindMapDiagramRequest;
use App\Http\Requests\UpdateMindMapDiagramRequest;

class MindMapDiagramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $mapDiagrams = MindMapDiagram::where('user_id', auth()->user()->id)->orderByDesc('created_at')->get();

        return view('mind-map.index', compact('mapDiagrams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMindMapDiagramRequest $request)
    {
        $mind_map              = new MindMapDiagram();
        $mind_map->title       = $request->title;
        $mind_map->user_id     = auth()->user()->id;
        $mind_map->description = $request->description;
        $mind_map->data        = $request->data;
        $mind_map->save();
    }

    /**
     * Display the specified resource.
     *
     * @param MindMapDiagram $mindMapDiagram
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MindMapDiagram $mindMap)
    {
        return ['mindMap' => $mindMap];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MindMapDiagram $mindMapDiagram)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMindMapDiagramRequest $request, MindMapDiagram $mindMapDiagram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MindMapDiagram $mindMapDiagram)
    {
        //
    }
}
