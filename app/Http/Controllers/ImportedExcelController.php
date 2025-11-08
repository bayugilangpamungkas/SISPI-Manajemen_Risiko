<?php

namespace App\Http\Controllers;

use App\Models\ImportedExcel;
use App\Http\Requests\StoreImportedExcelRequest;
use App\Http\Requests\UpdateImportedExcelRequest;

class ImportedExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $imported = ImportedExcel::paginate(20);
        $active = 7;
        return view('pr.importedExcel', compact('imported', 'active'));
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
     * @param  \App\Http\Requests\StoreImportedExcelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreImportedExcelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImportedExcel  $importedExcel
     * @return \Illuminate\Http\Response
     */
    public function show(ImportedExcel $importedExcel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImportedExcel  $importedExcel
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportedExcel $importedExcel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateImportedExcelRequest  $request
     * @param  \App\Models\ImportedExcel  $importedExcel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateImportedExcelRequest $request, ImportedExcel $importedExcel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportedExcel  $importedExcel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImportedExcel $importedExcel)
    {
        //
    }
}
