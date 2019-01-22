<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Entity\Taxonomy;
use Bolt\Utils\Str;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaxonomyFixtures extends Fixture implements DependentFixtureInterface
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config->get('taxonomies');
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadTaxonomies($manager);

        $manager->flush();
    }

    private function loadTaxonomies(ObjectManager $manager): void
    {
        $order = 1;

        foreach ($this->config as $taxonomyDefinition) {
            $options = empty($taxonomyDefinition['options']) ? $this->getDefaultOptions() : $taxonomyDefinition['options'];

            foreach ($options as $key => $value) {
                $taxonomy = Taxonomy::factory(
                    $taxonomyDefinition['slug'],
                    $key,
                    $value,
                    $taxonomyDefinition['has_sortorder'] ? $order++ : 0
                );

                $manager->persist($taxonomy);
                $reference = 'taxonomy_' . $taxonomyDefinition['slug'] . '_' . Str::slug($key);
                $this->addReference($reference, $taxonomy);
            }
        }
    }

    private function getDefaultOptions()
    {
        $options = ['action', 'adult', 'adventure', 'alpha', 'animals', 'animation', 'anime', 'architecture', 'art',
            'astronomy', 'baby', 'batshitinsane', 'biography', 'biology', 'book', 'books', 'business',
            'camera', 'cars', 'cats', 'cinema', 'classic', 'comedy', 'comics', 'computers', 'cookbook', 'cooking',
            'crime', 'culture', 'dark', 'design', 'digital', 'documentary', 'dogs', 'drama', 'drugs', 'education',
            'environment', 'evolution', 'family', 'fantasy', 'fashion', 'fiction', 'film', 'fitness', 'food',
            'football', 'fun', 'gaming', 'gift', 'health', 'hip', 'historical', 'history', 'horror', 'humor',
            'illustration', 'inspirational', 'internet', 'journalism', 'kids', 'language', 'law', 'literature', 'love',
            'magic', 'math', 'media', 'medicine', 'military', 'money', 'movies', 'mp3', 'murder', 'music', 'mystery',
            'news', 'nonfiction', 'nsfw', 'paranormal', 'parody', 'philosophy', 'photography', 'photos', 'physics',
            'poetry', 'politics', 'post-apocalyptic', 'privacy', 'psychology', 'radio', 'relationships', 'research',
            'rock', 'romance', 'rpg', 'satire', 'science', 'sciencefiction', 'scifi', 'security', 'self-help',
            'series', 'software', 'space', 'spirituality', 'sports', 'story', 'suspense', 'technology', 'teen',
            'television', 'terrorism', 'thriller', 'travel', 'tv', 'uk', 'urban', 'us', 'usa', 'vampire', 'video',
            'videogames', 'war', 'web', 'women', 'world', 'writing', 'wtf', 'zombies',
        ];

        return array_combine($options, $options);
    }
}
