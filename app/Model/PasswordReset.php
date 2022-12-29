<?php

namespace Model;

class PasswordReset
{

    private string $id;
    private string $user_id;
    private string $token;
    private string $created_at;

    /**
     * @param string $id
     * @param string $user_id
     * @param string $token
     * @param string $created_at
     */
    public function __construct(string $id, string $user_id, string $token, string $created_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->token = $token;
        $this->created_at = $created_at;
        dump("created");
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     */
    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
}
