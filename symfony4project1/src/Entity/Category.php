<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="categories")
     */
    private $posts;

    use TimestampableEntity;
    use SoftDeleteableEntity;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function __toString() {
        return $this->getId(). ": " . $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            $post->removeCategory($this);
        }

        return $this;
    }

    public function removeAllPosts(){
        foreach($this->posts as $post){
            $post->removeCategory($this);
        }
        $this->posts = new ArrayCollection();
        return $this;
    }

    public function addPostsFromArrayOfIds(array $ids, EntityManager $manager){
        foreach($ids as $id){
            /**
             * @var $post Post
             */
            $post = $manager->getRepository(Post::class)->find((int)$id);
            if(!empty($post)){
                $this->addPost($post);
            }
        }
    }
}
