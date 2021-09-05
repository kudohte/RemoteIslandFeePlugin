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
         * @ORM\Column(name="postal_codes", type="text", nullable=true)
         */
        private $postal_codes;

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
         * Set postal_codes.
         *
         * @return this
         */
        public function setPostalCodes($postal_codes)
        {
            $this->postal_codes = $postal_codes;

            return $this;
        }

        /**
         * Get postal_codes.
         *
         * @return string|null
         */
        public function getPostalCodes()
        {
            return $this->postal_codes;
        }

        public function getPostalCodeLists()
        {
            $postalCodeList = $this->getPostalCodes();
            $array = explode(',', $postalCodeList);
            return $array;
        }

    }
}