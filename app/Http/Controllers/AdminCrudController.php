<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

/** Shared admin CRUD. Each subclass only describes its model, fields, columns and rules. */
abstract class AdminCrudController extends Controller
{
    abstract protected function cfg($row = null): array;
    /** Return a reason string when the row can't be deleted, otherwise null. */
    abstract protected function inUse($row): ?string;

    public function index()
    {
        $c = $this->cfg();
        $rows = $c['model']::with($c['with'] ?? [])->get();
        return view('admin.crud.index', compact('c', 'rows'));
    }

    public function create() { return view('admin.crud.form', ['c' => $this->cfg(), 'row' => null]); }

    public function store(Request $r)
    {
        $c = $this->cfg();
        $c['model']::create($r->validate($c['rules']));
        return redirect()->route($c['route'].'.index')->with('ok', "{$c['singular']} added.");
    }

    public function edit($id)
    {
        $row = $this->cfg()['model']::findOrFail($id);
        return view('admin.crud.form', ['c' => $this->cfg($row), 'row' => $row]);
    }

    public function update(Request $r, $id)
    {
        $row = $this->cfg()['model']::findOrFail($id);
        $c = $this->cfg($row);
        $row->update($r->validate($c['rules']));
        return redirect()->route($c['route'].'.index')->with('ok', "{$c['singular']} updated.");
    }

    public function destroy($id)
    {
        $c = $this->cfg();
        $row = $c['model']::findOrFail($id);
        if ($why = $this->inUse($row)) {
            return back()->withErrors(['delete' => "Can't delete this {$c['singular']}: $why"]);
        }
        $row->delete();
        return back()->with('ok', "{$c['singular']} deleted.");
    }
}
