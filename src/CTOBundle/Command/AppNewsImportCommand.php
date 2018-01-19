<?php

namespace CTOBundle\Command;

use CTOBundle\Entity\Author;
use CTOBundle\Entity\News;
use CTOBundle\Repository\AuthorRepository;
use CTOBundle\Repository\TagsRepository;
use CTOBundle\Services\NewsAPIService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Proxies\__CG__\CTOBundle\Entity\Tags;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
class AppNewsImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:news-import')
            ->setDescription('<info>Импорт новостей из информационных сайтов с помощью api news</info>')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Импорт новостей из информационных сайтов с помощью api news</info>');
        $em = $this->getContainer()->get("doctrine")->getManager();
        $authorRepository = $em->getRepository(Author::class);
        $newsRepository = $em->getRepository(News::class);
        $tagReposirory = $em->getRepository(Tags::class);
        for ($i = 1; $i < 415; $i++) {
            $newsService = new NewsAPIService($this->getContainer(),$i);
            foreach ( $newsService->getData($i) as $article ) {
                try {
                    $paramsAuthor = array(
                        "author" => $article->author
                    );
                    $getAuthor = $authorRepository->existByName($article->author);
                    if ( $getAuthor == null && !empty($article->author) )
                    {
                        $getAuthor = $authorRepository->createFromNewsApi($paramsAuthor);
                    }
                    $tag = $tagReposirory->existByName($article->source->name);
                    if ( $tag == null && !empty($article->source->name))
                    {
                        $tag = $tagReposirory->createFromNewsApi(array("name" => $article->source->name));
                    }

                    $paramsNews = array(
                        "title"       => $article->title,
                        "description" => $article->description,
                        "date_create" => $article->publishedAt,
                        "url"         => $article->url,
                        "urlToImage"  => $article->urlToImage,
                        "author"      => $getAuthor,
                        "tag"          => array($tag)
                    );
                    $getNewsID = $newsRepository->existByName($article->title);
                    if ( !$getNewsID )
                    {
                        $getNewsID = $newsRepository->createFromNewsApi($paramsNews);
                    }
                }
                catch (\Doctrine\ORM\ORMException $exception) {
                    continue;
                }
            }
        }
    }

}
