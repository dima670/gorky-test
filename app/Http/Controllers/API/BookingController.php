<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order = new Order();

        //С какого значения показать
        if ($request->has('offset')) {
            $offset = (int) $request->offset;
            $order = $order->skip($offset);
        }

        // Ограничение на выборку
        if ($request->has('limit')) {
            $limit = (int) $request->limit;
            $order = $order->take($limit);
        }
        //Записи пользователя
        if ($request->has('user_id')) {
            $userId = (int) $request->user_id;
            $order = $order->where('user_id', $userId);
        }

        $orders = $order->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'nullable',
            'arrival_date' => 'required|date',
        ]);
        $user = User::find($request->user_id);
        $order = new Order();
        if ($user != null) {
            $order->user_id = $user->id;
        }
        $order->arrival_date = $request->arrival_date;
        $order->save();

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->fill($request->except(['id']));
        $order->save();

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }
}
