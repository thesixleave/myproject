# -*- coding: utf-8 -*-
# Generated by Django 1.10.5 on 2017-03-18 08:06
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('leaf', '0002_person'),
    ]

    operations = [
        migrations.CreateModel(
            name='Ubike',
            fields=[
                ('sno', models.CharField(max_length=20, primary_key=True, serialize=False)),
                ('sna', models.CharField(max_length=20, verbose_name='站名')),
                ('sarea', models.CharField(max_length=20, verbose_name='區域')),
                ('lat', models.CharField(max_length=30, verbose_name='緯度')),
                ('lng', models.CharField(max_length=30, verbose_name='經度')),
                ('ar', models.CharField(max_length=30, verbose_name='位置')),
                ('sareaen', models.CharField(max_length=30, verbose_name='區域英文')),
                ('snaen', models.CharField(max_length=30, verbose_name='站名英文')),
                ('aren', models.CharField(max_length=30, verbose_name='位置英文')),
            ],
        ),
    ]
