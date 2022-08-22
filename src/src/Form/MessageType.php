<?php

namespace App\Form;

use App\Entity\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageType extends AbstractType
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $subjectLabel = $this->translator->trans('message.subject');
        $messageLabel = $this->translator->trans('message.messages');
        $emailLabel = $this->translator->trans('message.email');
        $builder
            ->add('subject', TextType::class, ['label_format' => $subjectLabel])
            ->add('message', TextareaType::class, ['label_format' => $messageLabel])
            ->add('email', EmailType::class, ['label_format' => $emailLabel])
            ->add('save', SubmitType::class, ['label_format' => '%name%'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}