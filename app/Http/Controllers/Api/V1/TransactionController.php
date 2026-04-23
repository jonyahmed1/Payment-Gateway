<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $service;
    public function __construct(TransactionService $service) { $this->service = $service; }

    public function index(Request $request)
    {
        $query = Transaction::query()->with(['agent','number','maker','checker']);
        if ($request->filled('status')) $query->where('status',$request->status);
        if ($request->filled('agent')) $query->where('mfs_agent_id',$request->agent);
        if ($request->filled('q')) $query->where('trx_id','like','%'.$request->q.'%');
        return response()->json($query->paginate(25));
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $tx = $this->service->create($data, Auth::id());
        AuditLog::create(['user_id'=>Auth::id(),'action'=>'transaction.created','auditable_type'=>Transaction::class,'auditable_id'=>$tx->id,'data'=>$tx->toArray(),'ip'=>$request->ip()]);
        return response()->json($tx, 201);
    }

    public function show($id) {
        $tx = Transaction::with(['attachments','agent','number','maker','checker'])->findOrFail($id);
        return response()->json($tx);
    }

    // Maker verifies
    public function verify(Request $request, $id)
    {
        $tx = Transaction::findOrFail($id);
        $user = $request->user();
        // permission enforcement (use policies / spatie)
        if (!$user->can('transaction.verify')) abort(403);
        if ($tx->status !== Transaction::STATUS_PENDING) return response()->json(['message'=>'Only pending transactions can be verified.'], 422);

        $tx->update([
            'status' => Transaction::STATUS_VERIFIED,
            'maker_admin_id' => $user->id,
            'verified_at' => now(),
        ]);
        AuditLog::create(['user_id'=>$user->id,'action'=>'transaction.verified','auditable_type'=>Transaction::class,'auditable_id'=>$tx->id,'data'=>$tx->toArray(),'ip'=>$request->ip()]);
        return response()->json($tx);
    }

    // Checker approves
    public function approve(Request $request, $id)
    {
        $tx = Transaction::findOrFail($id);
        $user = $request->user();
        if (!$user->can('transaction.approve')) abort(403);
        // Checker cannot be same as maker (optional)
        if ($tx->maker_admin_id && $tx->maker_admin_id == $user->id) {
            return response()->json(['message'=>'Maker and checker must be different users.'], 422);
        }
        if (!in_array($tx->status, [Transaction::STATUS_VERIFIED, Transaction::STATUS_PENDING])) {
            return response()->json(['message'=>'Transaction cannot be approved in current status.'], 422);
        }

        // optional: enforce middleware for IP whitelist and 2FA on approval-sensitive endpoint
        $tx->update([
            'status' => Transaction::STATUS_APPROVED,
            'checker_admin_id' => $user->id,
            'approved_at' => now(),
        ]);
        AuditLog::create(['user_id'=>$user->id,'action'=>'transaction.approved','auditable_type'=>Transaction::class,'auditable_id'=>$tx->id,'data'=>$tx->toArray(),'ip'=>$request->ip()]);

        // Dispatch async job to notify integrators / call provider
        \App\Jobs\NotifyIntegrators::dispatch($tx);

        return response()->json($tx);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason'=>'required|string']);
        $tx = Transaction::findOrFail($id);
        $user = $request->user();
        if (!$user->can('transaction.reject')) abort(403);

        $tx->update([
            'status' => Transaction::STATUS_REJECTED,
            'rejected_at' => now(),
            'notes' => $request->reason,
        ]);
        AuditLog::create(['user_id'=>$user->id,'action'=>'transaction.rejected','auditable_type'=>Transaction::class,'auditable_id'=>$tx->id,'data'=>['notes'=>$request->reason],'ip'=>$request->ip()]);

        return response()->json($tx);
    }
}