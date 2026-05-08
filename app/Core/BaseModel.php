<?php
/**
 * Base Model — all models extend this.
 * Automatically scopes queries to tenant_id for data isolation.
 */
abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    /** Get current tenant_id from session (0 = super admin / no tenant) */
    protected static function tenantId(): int
    {
        return (int)($_SESSION['tenant_id'] ?? 0);
    }

    /**
     * Find all records for current tenant.
     */
    public static function all(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $sql    = "SELECT * FROM `" . static::$table . "` WHERE 1=1";
        $params = [];

        if (static::tenantId() > 0) {
            $sql     .= " AND tenant_id = ?";
            $params[] = static::tenantId();
        }

        foreach ($conditions as $col => $val) {
            $sql     .= " AND `$col` = ?";
            $params[] = $val;
        }

        if ($orderBy) $sql .= " ORDER BY $orderBy";
        if ($limit)   $sql .= " LIMIT $limit";
        if ($offset)  $sql .= " OFFSET $offset";

        return DB::query($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $sql    = "SELECT * FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?";
        $params = [$id];

        if (static::tenantId() > 0) {
            $sql     .= " AND tenant_id = ?";
            $params[] = static::tenantId();
        }

        return DB::queryOne($sql, $params);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $sql    = "SELECT * FROM `" . static::$table . "` WHERE `$column` = ?";
        $params = [$value];

        if (static::tenantId() > 0) {
            $sql     .= " AND tenant_id = ?";
            $params[] = static::tenantId();
        }

        return DB::queryOne($sql, $params);
    }

    public static function count(array $conditions = []): int
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `" . static::$table . "` WHERE 1=1";
        $params = [];

        if (static::tenantId() > 0) {
            $sql     .= " AND tenant_id = ?";
            $params[] = static::tenantId();
        }

        foreach ($conditions as $col => $val) {
            $sql     .= " AND `$col` = ?";
            $params[] = $val;
        }

        return (int)(DB::queryOne($sql, $params)['cnt'] ?? 0);
    }

    public static function create(array $data): int
    {
        if (static::tenantId() > 0 && !isset($data['tenant_id'])) {
            $data['tenant_id'] = static::tenantId();
        }
        if (!isset($data['created_by'])) {
            $data['created_by'] = $_SESSION['user_id'] ?? null;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::insert(static::$table, $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $where = [static::$primaryKey => $id];
        if (static::tenantId() > 0) {
            $where['tenant_id'] = static::tenantId();
        }
        return DB::update(static::$table, $data, $where);
    }

    public static function delete(int $id): int
    {
        $where = [static::$primaryKey => $id];
        if (static::tenantId() > 0) {
            $where['tenant_id'] = static::tenantId();
        }
        return DB::delete(static::$table, $where);
    }

    public static function paginate(int $page, int $perPage = 20, array $conditions = []): array
    {
        $total  = static::count($conditions);
        $offset = ($page - 1) * $perPage;
        $rows   = static::all($conditions, '', $perPage, $offset);

        return [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
            'from'         => $offset + 1,
            'to'           => min($offset + $perPage, $total),
        ];
    }
}
