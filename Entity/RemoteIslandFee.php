<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Customize\Entity;

namespace Plugin\RemoteIslandFeePlugin\Entity;
use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Plugin\RemoteIslandFeePlugin\Entity\RemoteIslandFee')) {
    /**
     * RemoteIslandFee
     *
     * @ORM\Table(name="plg_remote_island_fee")
     * @ORM\Entity(repositoryClass="Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository")
     */
    class RemoteIslandFee extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="fee", type="decimal", precision=12, scale=2)
         */
        private $fee;

        /**
         * @var string|null
         *
         * @ORM\Column(name="postal_code", type="text", nullable=true)
         */
        private $postal_code;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set fee.
         *
         * @param string|null $fee
         *
         * @return $this
         */
        public function setFee($fee)
        {
            $this->fee = $fee;

            return $this;
        }

        /**
         * Get fee.
         *
         * @return string|null
         */
        public function getFee()
        {
            return $this->fee;
        }

        /**
         * Set postal_code.
         *
         * @return this
         */
        public function setPostalCode($postal_code)
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode()
        {
            return $this->postal_code;
        }

    }
}