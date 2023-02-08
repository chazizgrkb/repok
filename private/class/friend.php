<?php

namespace rePok {

    enum FriendStatus: int
    {
        case PENDING = 0;
        case FRIENDS = 1;
    }

    class Friend
    {
        static function sendRequest($sender, $receiver): void
        {
            global $sql;
            $isFriend = $sql->fetchArray(
                $sql->query(
                    "SELECT * FROM friends WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)",
                    [$sender, $receiver, $receiver, $sender]
                ));
            if ($isFriend) {
                die("Either you've (or they've) already requested to add this user, or you are friends with them.");
            } else {
                $sql->query(
                    "INSERT INTO friends (sender, receiver, status, time) VALUES (?,?,?,?)",
                    [$sender, $receiver, FriendStatus::PENDING->value, time()]
                );
            }
        }
    }
}